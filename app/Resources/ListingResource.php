<?php

namespace EhxDirectorist\Resources;

use EhxDirectorist\Models\Category;
use EhxDirectorist\Models\Listing;
use EhxDirectorist\Models\Post;
use EhxDirectorist\Models\Tag;
use EhxDirectorist\Services\Arr;

class ListingResource
{
    public static function store($data)
    {

        $data['category_id'] = json_encode($data['category_id']);
        $data['tag_id'] = json_encode($data['tag_id']);
        $data['meta'] = json_encode($data['meta']);


        return Listing::create($data);
    }

    public static function update($data, $id)
    {
        $data['category_id'] = json_encode($data['category_id']);
        $data['tag_id'] = json_encode($data['tag_id']);
        $data['meta'] = json_encode($data['meta']);

        $listingModel = new Listing();

        $listing = $listingModel->find($id);

        if ($listing) {
            return $listing->update($data, $id);
        }

        return false;
    }

    public static function delete($id)
    {
        $listingModel = new Listing();
        $listing = $listingModel->find($id);

        if ($listing) {
            $listing->delete($id);
            return true;
        }

        return false;
    }

    public static function get($id)
    {
        $listing = (new Listing())->find($id);
        if (!$listing) {
            return null;
        }
        $listing->meta = json_decode($listing->meta);
        $listing->category_id = json_decode($listing->category_id);
        $listing->tag_id = json_decode($listing->tag_id);
        return $listing->toArray();
    }

    public static function getAll($perPage, $page, $search, $category_ids, $tag_ids)
    {
        $listingModel = new Listing();

        // Get listings with filters (with fallback to all if nothing matched)
        $listings = $listingModel->filterForListing($perPage, $page, $search, $category_ids, $tag_ids);

        foreach ($listings['data'] as $listing) {
            $listing->category_id  = is_string($listing->category_id) ? json_decode($listing->category_id, true) : $listing->category_id;
            $listing->meta         = is_string($listing->meta) ? json_decode($listing->meta, true) : $listing->meta;
            $listing->tag_id       = is_string($listing->tag_id) ? json_decode($listing->tag_id, true) : $listing->tag_id;
            $listing->tags         = (new Tag())->whereIn('id', $listing->tag_id)->get()->toArray();
            $listing->categories   = (new Category())->whereIn('id', $listing->category_id)->get()->toArray();
        }

        return $listings;
    }
}
