<?php

namespace App\Service\Pagination;

use App\DTO\Paginator\PaginatorDTO;
use App\Repository\Interface\SearchInterface;


class PaginationService
{

    public static function paginate(SearchInterface $repo, $paramFetcher, $request, callable $mapper, $option = [])
    {
        $limit = $paramFetcher->get('limit');
        $pager = $repo->search(
            $paramFetcher->get('keyword'),
            $paramFetcher->get('order'),
            $limit,
            $paramFetcher->get('offset'),
            $option
        );
        $data = array_map($mapper, $pager);
        $count = $repo->countValue($option);

        $countPage = ceil($count/$limit);

        $offsetPrev = $paramFetcher->get('offset') - $limit;

        $offsetNext = $paramFetcher->get("offset") + $limit;

        $url = $request->getPathInfo();

        return new PaginatorDTO($data, $count, $countPage, $limit, $offsetPrev, $offsetNext, $url );

    }

}