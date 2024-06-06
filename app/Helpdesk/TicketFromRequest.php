<?php

namespace App\Helpdesk;

use App\Helpers\ConfigHelper;
use App\Helpers\FileUploadHelper;
use App\Models\Ticket;
use App\Models\TicketFields;
use App\Models\TicketFile;
use App\Models\TicketParticipant;
use App\Notifications\NewTicketParticipantNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class TicketFromRequest
{
    public Request $request;
    protected ?array $approvals;
    protected ?array $observers;
    protected FormFieldsCollection $fieldsCollection;
    protected string $subject;
    protected string $content;
    protected int $categoryId;
    protected int $departmentId;
    protected ?string $customLocation;
    protected int $officeId;
    protected ?int $roomId;
    protected int $userId;
    protected int $priority;
    protected int $number;
    protected bool $needApproval;
    protected int $status;
    protected bool $valid = false;
    protected ?array $files = null;

    /**
     * @param Request $request
     * @throws ValidationException
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->number = $this->makeNumber();
        $this->categoryId = $request->get('category_id');
        $this->departmentId = $request->get('department_id');
        $this->officeId = $request->get('office_id');
        $this->roomId = $request->get('room_id') ?? null;
        $this->customLocation = $request->get('custom_location') ?? null;
        $this->userId = $request->get('user_id');
        $this->subject = $request->get('subject');
        $this->content = $request->get('content');
        $this->fieldsCollection = new FormFieldsCollection($request);
        $this->approvals = $request->get('approvals');
        $this->observers = $request->get('observers');
        $this->needApproval = $this->approvals !== null && count($this->approvals) > 0;
        $this->status = $this->needApproval ? TicketStatus::IN_APPROVAL : TicketStatus::NEW;
        $this->priority = TicketPriority::NORMAL;
        $this->files = $request->file('files');
    }

    /**
     * Set Ticket number
     * @return string
     */
    public function makeNumber()
    {
        return Carbon::now()->format('ymdhisu');
    }

    /**
     * @return Ticket
     * @throws \Throwable
     */
    public function create(): Ticket
    {
        return DB::transaction(function () {
            $ticket = Ticket::create([
                'subject' => $this->subject,
                'content' => $this->content,
                'user_id' => $this->userId,
                'category_id' => $this->categoryId,
                'department_id' => $this->departmentId,
                'room_id' => $this->roomId,
                'custom_location' => $this->customLocation,
                'office_id' => $this->officeId,
                'priority' => $this->priority,
                'status' => $this->status,
                'need_approval' => $this->needApproval
            ]);

            if (!empty($this->files)) {
                foreach ($this->files as $file) {
                    //TicketFile::create();
                    /** @var UploadedFile $file */
                    $storage = FileUploadHelper::ticketFilesStorage();
                    if (!$storage->directoryExists($ticket->id)) {
                        $storage->createDirectory($ticket->id);
                    }
                    $path = $ticket->id . DIRECTORY_SEPARATOR . $file->getClientOriginalName();
                    if ($storage->put($path, $file->getContent())) {
                        TicketFile::updateOrCreate([
                            'user_id' => $ticket->user_id,
                            'ticket_id' => $ticket->id,
                            'name' => $file->getClientOriginalName()
                        ], [
                            'path' => $path,
                            'size' => $file->getSize(),
                            'extension' => $file->getClientOriginalExtension()
                        ]);
                    }
                }
            }

            if (count($this->fieldsCollection->items) > 0) {
                foreach ($this->fieldsCollection->items as $item) {
                    if ($item->value instanceof UploadedFile) {
                        self::addFileField($ticket, $item);
                    } elseif (is_string($item->value)) {
                        self::addTextField($ticket, $item);
                    }
                }
            }

            self::addRequester($ticket->id, $this->userId);
            if (!is_null($this->approvals)) {
                foreach ($this->approvals as $userId) {
                    $p = self::addApproval($ticket->id, $userId);
                    $p->user->notify((new NewTicketParticipantNotification($p, $this->request->user()))->afterCommit());
                    //dd(123);
                }
            }

            if (!is_null($this->observers)) {
                foreach ($this->observers as $observer) {
                    $p = self::addObserver($ticket->id, $observer);
                    $p->user->notify((new NewTicketParticipantNotification($p, $this->request->user()))->afterCommit());
                }
            }

            return $ticket;
        });
    }

    /**
     * @param Ticket $ticket
     * @param FormFiled $field
     * @return \Illuminate\Database\Eloquent\Model|TicketFields|null
     */
    public function addFileField(Ticket $ticket, FormFiled $field): \Illuminate\Database\Eloquent\Model|TicketFields|null
    {
        $path = TicketsStorage::createFile($ticket->id, $field->value);
        if (!is_null($path)) {
            $field->value = $path;
            return self::addTextField($ticket, $field);
        }
        return null;
    }

    /**
     * @param Ticket $ticket
     * @param FormFiled $field
     * @return \Illuminate\Database\Eloquent\Model|TicketFields
     */
    public function addTextField(Ticket $ticket, FormFiled $field): \Illuminate\Database\Eloquent\Model|TicketFields
    {
        return TicketFields::create([
            'ticket_id' => $ticket->id,
            'category_id' => $ticket->category_id,
            'field_id' => $field->id,
            'content' => $field->value
        ]);
    }

    /**
     * @param int $ticketId
     * @param int $id
     * @return TicketParticipant|null
     */
    public static function addApproval(int $ticketId, int $id): ?TicketParticipant
    {
        return self::addParticipant($ticketId, $id, Participant::APPROVAL);
    }

    /**
     * @param int $ticketId
     * @param int $id
     * @return TicketParticipant|null
     */
    public static function addRequester(int $ticketId, int $id): ?TicketParticipant
    {
        return self::addParticipant($ticketId, $id, Participant::REQUESTER);
    }

    /**
     * @param int $ticketId
     * @param int $id
     * @return TicketParticipant|null
     */
    public static function addObserver(int $ticketId, int $id): ?TicketParticipant
    {
        return self::addParticipant($ticketId, $id, Participant::OBSERVER);
    }

    /**
     * @param int $ticketId
     * @param int $id
     * @return TicketParticipant|null
     */
    public static function addAssignee(int $ticketId, int $id): ?TicketParticipant
    {
        return self::addParticipant($ticketId, $id, Participant::ASSIGNEE);
    }

    /**
     * @param int $ticketId
     * @param int $id
     * @param int $role
     * @return TicketParticipant
     */
    private static function addParticipant(int $ticketId, int $id, int $role): ?TicketParticipant
    {
        $participant = new TicketParticipant();
        $participant->ticket_id = $ticketId;
        $participant->user_id = $id;
        $participant->role = $role;
        try {
            $participant->save();
            return $participant;
        } catch (\Exception $exception) {
            Log::error($exception);
            dd($exception->getMessage());
        }
        return null;
        /*return TicketParticipant::insertOrIgnore([
            'ticket_id' => $ticketId,
            'user_id' => $id,
            'role' => $role,
        ]);*/
    }

    /**
     * @param Request $request
     * @return bool
     * @throws ValidationException
     */
    public function validate(Request $request): bool
    {
        $this->validateMainFields($request);
        $this->validateCategoryFields();
        return $this->valid;
    }

    /**
     * @param Request $request
     * @return void
     * @throws ValidationException
     */
    private function validateMainFields(Request $request)
    {
        $keys = ['subject', 'content', 'category_id', 'office_id'];
        $rules = [];
        // todo validate category_id
        foreach ($keys as $key) {
            if ($key === 'subject' || $key === 'content') {
                $rules[$key] = 'required|min:10';
            } else {
                $rules[$key] = 'required';
            }
        }
        if (!empty($this->files)) {
            $keys[] = 'files';
            $maxFileSize = ConfigHelper::getMaxFileSize();
            $allowedMimes = ConfigHelper::getAllowedMimes();
            $rules['files'] = 'array|max:5';
            $rules['files.*'] = 'max:' . $maxFileSize . '|mimes:' . implode(',', $allowedMimes);
        }
        // TODO custom messages
        // $messages = [];
        $fields = $request->only($keys);
        $v = Validator::make($fields, $rules);
        $v->validate();
        $this->valid = true;
    }

    /**
     * @return void
     * @throws ValidationException
     */
    private function validateCategoryFields()
    {
        $this->valid = $this->fieldsCollection->validate();
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @return FormFieldsCollection
     */
    public function getFieldsCollection(): FormFieldsCollection
    {
        return $this->fieldsCollection;
    }

    /**
     * @return int
     */
    public function getCategoryId(): int
    {
        return $this->categoryId;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }
}
