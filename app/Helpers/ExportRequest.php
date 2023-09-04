<?php

namespace App\Helpers;

use App\Models\Ticket;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ExportRequest
{
    protected Request $request;
    protected Builder $builder;
    protected ?string $term = null;
    protected string $criteria = 'all';
    protected RequestBuilder $requestBuilder;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->builder = Ticket::with([
            'fields',
            'category',
            'requester',
            'assignees',
            'observers',
            'approvals'
        ]);
        $this->requestBuilder = RequestBuilder::results($request, $this->builder);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Support\Collection
     */
    public static function getData(Request $request)
    {
        return (new self($request))->requestBuilder->get();
    }

    /**
     * @return RequestBuilder
     */
    public function getRequestBuilder(): RequestBuilder
    {
        return $this->requestBuilder;
    }

    /**
     * @return bool
     */
    public function hasCategoryFilter(): bool
    {
        return $this->requestBuilder->getCategoryId() > 0;
    }
}
