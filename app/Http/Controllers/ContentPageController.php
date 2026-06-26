<?php

namespace App\Http\Controllers;

use App\Models\AgendaItem;
use App\Models\Announcement;
use App\Models\Facility;
use App\Models\GalleryItem;
use App\Models\MenuItem;
use App\Models\NewsPost;
use App\Models\ResourceLink;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\StaffMember;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\View;

class ContentPageController extends Controller
{
    public function resources(): View
    {
        return $this->indexView('resources', 'Digilib e-Resources', ResourceLink::class, 'description');
    }

    public function resource(ResourceLink $resource): View
    {
        return $this->showView('resources', 'Digilib e-Resources', $resource, 'description', 'body');
    }

    public function services(): View
    {
        return $this->indexView('services', 'Layanan', Service::class, 'description');
    }

    public function service(Service $service): View
    {
        return $this->showView('services', 'Layanan', $service, 'description', 'body');
    }

    public function facilities(): View
    {
        return $this->indexView('facilities', 'Fasilitas', Facility::class, 'description');
    }

    public function facility(Facility $facility): View
    {
        return $this->showView('facilities', 'Fasilitas', $facility, 'description', 'body');
    }

    public function announcements(): View
    {
        return $this->indexView('announcements', 'Pengumuman', Announcement::class, 'excerpt');
    }

    public function announcement(Announcement $announcement): View
    {
        return $this->showView('announcements', 'Pengumuman', $announcement, 'excerpt', 'body');
    }

    public function agenda(): View
    {
        return $this->indexView('agenda', 'Agenda', AgendaItem::class, 'description');
    }

    public function agendaItem(AgendaItem $agendaItem): View
    {
        return $this->showView('agenda', 'Agenda', $agendaItem, 'description', 'body');
    }

    public function news(): View
    {
        return $this->indexView('news', 'Berita', NewsPost::class, 'excerpt');
    }

    public function newsPost(NewsPost $newsPost): View
    {
        return $this->showView('news', 'Berita', $newsPost, 'excerpt', 'body');
    }

    public function galleries(): View
    {
        return $this->indexView('galleries', 'Galeri', GalleryItem::class, 'description');
    }

    public function gallery(GalleryItem $gallery): View
    {
        return $this->showView('galleries', 'Galeri', $gallery, 'description', 'description');
    }

    public function staff(): View
    {
        return $this->indexView('staff', 'Staff', StaffMember::class, 'position');
    }

    public function staffMember(StaffMember $staffMember): View
    {
        return $this->showView('staff', 'Staff', $staffMember, 'position', 'bio');
    }

    /**
     * @param  class-string<Model>  $model
     */
    private function indexView(string $type, string $title, string $model, string $excerptColumn): View
    {
        return view('content.index', [
            'site' => SiteSetting::current(),
            'menus' => MenuItem::active()->ordered()->get(),
            'type' => $type,
            'title' => $title,
            'excerptColumn' => $excerptColumn,
            'items' => $model::active()->ordered()->paginate(9),
        ]);
    }

    private function showView(string $type, string $sectionTitle, Model $item, string $excerptColumn, string $bodyColumn): View
    {
        abort_unless((bool) $item->is_active, 404);

        $model = $item::class;

        return view('content.show', [
            'site' => SiteSetting::current(),
            'menus' => MenuItem::active()->ordered()->get(),
            'type' => $type,
            'sectionTitle' => $sectionTitle,
            'item' => $item,
            'excerptColumn' => $excerptColumn,
            'bodyColumn' => $bodyColumn,
            'relatedItems' => $model::active()->ordered()->whereKeyNot($item->getKey())->take(5)->get(),
        ]);
    }
}
