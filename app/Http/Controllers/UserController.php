<?php

namespace App\Http\Controllers;

use App\Exceptions\LdapAccessDeniedException;
use App\Exceptions\LdapEntityNotFountException;
use App\Helpdesk\Participant;
use App\Helpers\AclHelper;
use App\Helpers\LdapHelper;
use App\Helpers\MailRecipients;
use App\Mail\RequestUserInfoUpdateMail;
use App\Models\AdminDepartments;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use LdapRecord\LdapRecordException;
use LdapRecord\Models\ModelDoesNotExistException;
use Mockery\Exception;

class UserController extends Controller
{
    public function getUser(): User|Authenticatable|null
    {
        if (Auth::check()) {
            /** @var User $user */
            return User::find(Auth::id())
                ->load('departments')
                ->load('room')
                ->load('office');
        }

        return null;
    }

    /**
     * @throws LdapEntityNotFountException
     * @throws AuthenticationException
     * @throws LdapAccessDeniedException
     */
    public function authUser(Request $request)
    {
        $credentials = [
            'samaccountname' => $request->get('username'),
            'password' => $request->get('password'),
        ];
        if (in_array(null, $credentials)) {
            throw new Exception(__('validation.username_or_password_incorrect'));
        }
        try {
            if (LdapHelper::isHelpdeskUser($request->get('username'))) {
                if (Auth::attempt($credentials)) {
                    $user = User
                        ::find(Auth::id())
                        ->load('departments')
                        ->load('room')
                        ->load('office');
                    $user->is_admin = LdapHelper::isHelpdeskAdmin($user->username);
                    $user->is_super_admin = AclHelper::isSuperAdmin();
                    $user->save();
                    return $user;
                } else {
                    throw new AuthenticationException();
                }
            } else {
                throw new LdapAccessDeniedException();
            }
        } catch (Exception $e) {
            throw new AuthenticationException();
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->to('/');
    }

    /**
     * @param Request $request
     * @return User|Authenticatable|null
     * @throws LdapRecordException
     * @throws ModelDoesNotExistException
     */
    public function editProfile(Request $request): User|Authenticatable|null
    {
        /** @var User $user */
        $user = Auth::user();
        $user->update($request->all());
        $user->load('departments');
        $user->load('room');
        $user->load('office');
        return $user;
    }

    public function updateAvatar(Request $request): User|Authenticatable|null
    {
        $user = $this->getUser();
        $file = $request->file('avatar');
        $coordinates = $request->get('coordinates');
        $path = '/private/avatars/' . Auth::id();
        $original = $path . DIRECTORY_SEPARATOR . 'original.jpg';
        $thumb = $path . DIRECTORY_SEPARATOR . 'thumb.jpg';
        if (!Storage::directoryExists($path)) {
            Storage::makeDirectory($path);
        }
        if (self::cropPhoto($file, $coordinates, [
            'original' => Storage::path($original),
            'thumb' => Storage::path($thumb),
        ])) {
            $user->photo = '/avatars/' . $user->id;
            $user->save();
        }
        return $user;
    }

    public function getAvatar(int $id, int $size = 300)
    {
        $path = '/private/avatars/' . $id;
        $thumb = $path . DIRECTORY_SEPARATOR . 'thumb.jpg';
        if (Storage::exists($thumb)) {
            return Image::read(Storage::path($thumb))->scale($size)->toJpeg();
        } else {
            User::where('id', $id)->update(['photo' => '']);
            return null;
        }
    }

    public static function cropPhoto(UploadedFile $file, array $coords, array $path): bool
    {
        $w = $coords['width'];
        $h = $coords['height'];
        $x = $coords['left'];
        $y = $coords['top'];
        $img = Image::read($file);
        $imgWidth = $img->width();
        $imgHeight = $img->height();
        $save_path = $path['original'];
        $save_thumb_path = $path['thumb'];
        $r = Image::read($file)->resize($imgWidth, $imgHeight)->save($save_path);
        if (Image::read($r)->crop($w, $h, $x, $y)->save($save_thumb_path)) {
            return true;
        }
        return false;
    }

    public function searchUsers(Request $request): Collection
    {
        $term = $request->get('term');
        $department = $request->get('department');
        $users = User::query();
        $users->select(['users.id', 'photo', 'email', 'firstname', 'lastname', 'position', 'department', 'organization'])->where(function (Builder $builder) use ($term) {
            $builder->orWhere('email', 'LIKE', "%" . $term . "%");
            $builder->orWhere('firstname', 'LIKE', "%" . $term . "%");
            $builder->orWhere('lastname', 'LIKE', "%" . $term . "%");
        });
        if (!is_null($department)) {
            $users->join('admin_departments', 'admin_id', '=', 'users.id');
            $users->where('department_id', $department);
        }
        //$users->ddRawSql();
        return $users->limit(100)->get();
    }

    public function getAdministrators()
    {
        return Participant::getAdministrators()->load('departments');
    }

    public function addAccess(Request $request)
    {
        $data = [
            'admin_id' => $request->get('admin_id'),
            'department_id' => $request->get('department_id')
        ];
        return AdminDepartments::updateOrCreate($data, [
            'department_id' => $request->get('department_id')
        ]);
    }

    public function deleteAccess(Request $request)
    {
        return AdminDepartments
            ::where(
                'admin_id', $request->get('admin_id')
            )
            ->where(
                'department_id', $request->get('department_id')
            )
            ->delete();
    }

    public function requestUpdates(Request $request)
    {
        try {
            Mail::send(new RequestUserInfoUpdateMail($request->user(), $request->get('message')));
        } catch (\Exception $exception) {
            Log::error(__METHOD__ . ' ' . $exception->getMessage());
            throw $exception;
        }

    }
}
