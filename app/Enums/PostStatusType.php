<?php

namespace App\Enums;


enum PostStatusType: string {
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
}