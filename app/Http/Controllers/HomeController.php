<?php

namespace App\Http\Controllers;

use App\Models\AgendaItem;
use App\Models\Announcement;
use App\Models\Banner;
use App\Models\Facility;
use App\Models\GalleryItem;
use App\Models\HeroSlide;
use App\Models\MenuItem;
use App\Models\NewsPost;
use App\Models\Partner;
use App\Models\ResourceLink;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\StaffMember;
use App\Models\Statistic;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('home', [
            'site' => SiteSetting::current(),
            'menus' => MenuItem::active()->ordered()->get(),
            'heroSlides' => HeroSlide::active()->ordered()->get(),
            'resources' => ResourceLink::active()->ordered()->get(),
            'banners' => Banner::active()->ordered()->get(),
            'services' => Service::active()->ordered()->take(3)->get(),
            'facilities' => Facility::active()->ordered()->take(6)->get(),
            'staffMembers' => StaffMember::active()->ordered()->take(4)->get(),
            'announcements' => Announcement::active()->ordered()->take(3)->get(),
            'agendaItems' => AgendaItem::active()->ordered()->take(3)->get(),
            'newsPosts' => NewsPost::active()->ordered()->take(3)->get(),
            'galleryItems' => GalleryItem::active()->ordered()->take(4)->get(),
            'statistics' => Statistic::active()->ordered()->take(4)->get(),
            'partners' => Partner::active()->ordered()->get(),
        ]);
    }
}
