<?php

namespace App\Providers;

use App\Models\Area;
use App\Models\BookDecoration;
use App\Models\BookDesign;
use App\Models\BookDesignCategory;
use App\Models\BookType;
use App\Models\City;
use App\Models\Diploma;
use App\Models\DiplomaMajor;
use App\Models\Governorate;
use App\Models\Major;
use App\Models\Svg;
use App\Models\SvgCategory;
use App\Models\University;
use App\Observers\AreaObserver;
use App\Observers\BookDecorationObserver;
use App\Observers\BookDesignCategoryObserver;
use App\Observers\BookDesignObserver;
use App\Observers\BookTypeObserver;
use App\Observers\CityObserver;
use App\Observers\DiplomaObserver;
use App\Observers\DiplomaMajorObserver;
use App\Observers\GovernorateObserver;
use App\Observers\MajorObserver;
use App\Observers\SvgCategoryObserver;
use App\Observers\SvgObserver;
use App\Observers\UniversityObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        BookType::observe(BookTypeObserver::class);
        BookDesign::observe(BookDesignObserver::class);
        BookDesignCategory::observe(BookDesignCategoryObserver::class);
        Svg::observe(SvgObserver::class);
        SvgCategory::observe(SvgCategoryObserver::class);
        University::observe(UniversityObserver::class);
        Major::observe(MajorObserver::class);
        BookDecoration::observe(BookDecorationObserver::class);
        Diploma::observe(DiplomaObserver::class);
        DiplomaMajor::observe(DiplomaMajorObserver::class);
        Governorate::observe(GovernorateObserver::class);
        City::observe(CityObserver::class);
        Area::observe(AreaObserver::class);
    }
}
