<?php
/*
* Activities Listing Page 
*/


// Activities listing according to Destination
$act_bread = $act_list = '';
$activity_hero = '';
$activity_package_cards = '';
$activity_travel_guides = '';
if (defined('ACTIVITIES_PAGE') and !empty($_REQUEST['slug'])) {
    $slug = addslashes($_REQUEST['slug']);
    $destt = Destination::find_by_slug($slug);
    if ($destt) {
        $activities = Activities::get_actitvityby($destt->id);
    } else {
        $activities = false;
    }

    if ($activities) {
        $act_bread .= '
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="' . BASE_URL . 'home"><i class="fas fa-home"></i></a></li>
                    <li class="breadcrumb-item active"><a href="#">Activities</a></li>
                </ol>
            </nav>
        ';

        foreach ($activities as $activity) {
            $img = '';
            $file_path = SITE_ROOT . "images/activities/" . $activity->image;
            if (file_exists($file_path) and !empty($activity->image)) {
                $img = '
                    <div class="image">
                        <img src="' . IMAGE_PATH . 'activities/' . $activity->image . '" alt="' . $activity->title . '" style="height:450px"/>
                    </div>
                ';
            }
            $content = explode('<hr id="system_readmore" style="border-style: dashed; border-color: orange;" />', $activity->content);
            $act_list .= '
                <div class="col">
                    <figure class="tour-grid-item-01">
                        <a href="' . BASE_URL . 'activity/' . $activity->slug . '">
                            ' . $img . '
                            <figcaption class="content ">
                                <h5>' . $activity->title . '</h5>
                                </br>
                                ' . $content[0] . '
                            </figcaption>
                        </a>
                    </figure>
                </div>
            ';
        }
    } else {
        $act_list .= '
            <div class="col">
                No Activities Found
            </div>
        ';
    }
} else {
    $act_list .= '
            <div class="col">
                No Activities Found
            </div>
        ';
}

$jVars['module:activity-list-breadcrumb'] = $act_bread;
$jVars['module:activity-list'] = $act_list;

// Dynamic modules for template/web/activity-list.html
$heroTitle = '';
$heroDescription = '';
$packageRows = array();
$content = array();

if (defined('ACTIVITIES_PAGE') and !empty($_REQUEST['slug'])) {
    $slug = addslashes($_REQUEST['slug']);
    
    // First try to find as Activity
    $selectedActivity = Activities::find_by_slug($slug);
    if ($selectedActivity) {
        $heroTitle = $selectedActivity->title;
        $content = explode('<hr id="system_readmore" style="border-style: dashed; border-color: orange;" />', $selectedActivity->content);
        if (!empty($selectedActivity->content)) {
            $heroDescription = strip_tags($selectedActivity->content);
        }
        $packageRows = Package::get_filterpkg_by('', $selectedActivity->id);
    } else {
        // If not activity, maybe it's a destination and we want packages for it?
        // Or we just default to some packages.
        $destt = Destination::find_by_slug($slug);
        if ($destt) {
            $heroTitle = $destt->title;
            $packageRows = Package::get_filterpkg_by($destt->id);
        }
    }
}

if (empty($packageRows)) {
    $packageRows = Package::getPackage(9);
}

if ($packageRows) {
    if ($heroTitle) {
        $banner_img = '';
        if (isset($selectedActivity) && $selectedActivity) {
            $file_path = SITE_ROOT . 'images/activities/banner/' . $selectedActivity->banner_image;
            if (!empty($selectedActivity->banner_image) && file_exists($file_path)) {
                $banner_img = IMAGE_PATH . 'activities/banner/' . $selectedActivity->banner_image;
            } else {
                $file_path2 = SITE_ROOT . 'images/activities/' . $selectedActivity->image;
                if (!empty($selectedActivity->image) && file_exists($file_path2)) {
                    $banner_img = IMAGE_PATH . 'activities/' . $selectedActivity->image;
                }
            }
        }
        $hero_bg = !empty($banner_img) ? 'style="background-image: linear-gradient(rgba(0,0,0,0.2), rgba(0,0,0,0.4)), url(' . $banner_img . ');"' : '';

        global $siteRegulars;
        if (!empty($siteRegulars->contact_info2)) {
            $mobile_arr = explode(',', $siteRegulars->contact_info2);
            $tel_link = preg_replace('/\s+/', '', trim($mobile_arr[0]));
        } elseif (!empty($siteRegulars->contact_info)) {
            $contacts_arr = explode('<br>', $siteRegulars->contact_info);
            $cleanContact = preg_replace('/\s*\(.*?\)/', '', trim($contacts_arr[0]));
            $tel_link = preg_replace('/\s+/', '', str_replace(',', '', $cleanContact));
        }
        $wa_number = preg_replace('/[^\d]/', '', $tel_link);
        $href = !empty($wa_number) ? 'https://wa.me/' . $wa_number : 'tel:' . $tel_link;

        $activity_hero .= '
            <section class="nepal-hero-content mt-4">
                <section class="hero" ' . $hero_bg . '>
                    <div class="overlay"></div>

                    <div class="activity_hero">
                        <h1 class="hero-title-trapezium">' . $heroTitle . '</h1>

                           ' . (isset($content[0]) ? $content[0] : '') . '


                         <div class="hero-buttons">
                        <a href="' . $href . '" target="_blank" class="btn btn-secondary">Talk to a Travel Advisor</a>
                    </div>

                    ' . (isset($content[1]) ? $content[1] : '') . '
                    </div>
                </section>
            </section>
        ';
    }

    foreach ($packageRows as $packageRow) {
        $file_path = SITE_ROOT . 'images/package/' . $packageRow->image;
        $img = (!empty($packageRow->image) and file_exists($file_path)) ? IMAGE_PATH . 'package/' . $packageRow->image : IMAGE_PATH . 'static/home-destination.jpg';

        $destinationTitle = !empty($packageRow->destinationId) ? Destination::field_by_id((int)$packageRow->destinationId, 'title') : '';
        $activityTitle = !empty($packageRow->activityId) ? Activities::field_by_id((int)$packageRow->activityId, 'title') : '';
        $accomodation = !empty($packageRow->accomodation) ? $packageRow->accomodation : 'N/A';
        $difficulty = !empty($packageRow->difficulty) ? $packageRow->difficulty : 'Moderate';
        $days = !empty($packageRow->days) ? (int)$packageRow->days : 0;

        $destinationTitleClean = trim(strip_tags((string)$destinationTitle));
        $activityTitleClean = trim(strip_tags((string)$activityTitle));
        $accomodationClean = trim(strip_tags((string)$accomodation));
        $difficultyClean = trim(strip_tags((string)$difficulty));

        $destinationTitleDisplay = (strlen($destinationTitleClean) > 32) ? substr($destinationTitleClean, 0, 29) . '...' : $destinationTitleClean;
        $activityTitleDisplay = (strlen($activityTitleClean) > 32) ? substr($activityTitleClean, 0, 29) . '...' : $activityTitleClean;
        $accomodationDisplay = $accomodationClean;
        $difficultyDisplay = $difficultyClean;

        $popularBadge = ($packageRow->popular == 1) ? '<span class="badge">POPULAR</span>' : '';

        $activity_package_cards .= '
            <div class="package-card">
                <div class="image-wrapper">
                    <img src="' . $img . '" alt="' . htmlspecialchars($packageRow->title, ENT_QUOTES, 'UTF-8') . '">
                    ' . $popularBadge . '
                </div>

                <div class="card-content">
                    <h3 class="title">' . htmlspecialchars($packageRow->title, ENT_QUOTES, 'UTF-8') . '</h3>

                            <div class="card-info-wrapper">

                                <div class="info-row">
                                    <div class="info">
                                        <span class="label">Destination</span>
                                        <span class="value green" title="' . htmlspecialchars($destinationTitleClean, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($destinationTitleDisplay, ENT_QUOTES, 'UTF-8') . '</span>
                                    </div>
                                    <div class="row-divider"></div>
                                    <div class="info text-right">
                                        <span class="label">Accomodations</span>
                                        <span class="value green" title="' . htmlspecialchars($accomodationClean, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($accomodationDisplay, ENT_QUOTES, 'UTF-8') . '</span>
                                    </div>
                                </div>

                                <div class="action-row">
                                    <span class="days-badge">' . $days . ' days</span>
                                    <a href="' . BASE_URL . 'package/' . $packageRow->slug . '" class="explore_btn">
                                        <p>Explore</p>
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                        </svg>
                                    </a>
                                </div>
                                <div class="info-row">';
        if ($activityTitleDisplay) {
            $activity_package_cards .= '
                                    <div class="info">
                                        <span class="label">Activities</span>
                                        <span class="value green" title="' . htmlspecialchars($activityTitleClean, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($activityTitleDisplay, ENT_QUOTES, 'UTF-8') . '</span>
                                    </div>
                                    <div class="row-divider"></div>';
        }
        $activity_package_cards .= '
                                    <div class="info text-right">
                                        <span class="label">Difficulty-level</span>
                                        <span class="value green" title="' . htmlspecialchars($difficultyClean, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($difficultyDisplay, ENT_QUOTES, 'UTF-8') . '</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>';
    }
}

if (empty($activity_package_cards)) {
    $activity_package_cards = '<div class="col-12 text-center"><p>No packages found for this activity right now.</p></div>';
}


if (isset($selectedActivity) && $selectedActivity) {
    $hasMeaningfulContent = function ($html) {
        if ($html === null) {
            return false;
        }

        $plain = html_entity_decode(strip_tags((string)$html), ENT_QUOTES, 'UTF-8');
        $plain = str_replace(array("\xc2\xa0", '&nbsp;'), ' ', $plain);
        return trim($plain) !== '';
    };

    $tabs = array();

    if ($hasMeaningfulContent($selectedActivity->packing_essentials)) {
        $tabs[] = array(
            'id' => 'todo',
            'title' => 'Packing Essentials',
            'content' => $selectedActivity->packing_essentials
        );
    }

    if ($hasMeaningfulContent($selectedActivity->money_expenses)) {
        $tabs[] = array(
            'id' => 'visit',
            'title' => 'Money & Expenses',
            'content' => $selectedActivity->money_expenses
        );
    }

    if ($hasMeaningfulContent($selectedActivity->best_time_visit)) {
        $tabs[] = array(
            'id' => 'stay',
            'title' => 'Best Time to Visit',
            'content' => $selectedActivity->best_time_visit
        );
    }

    if (!empty($tabs)) {
        $tabNavHtml = '';
        $tabContentHtml = '';

        foreach ($tabs as $index => $tab) {
            $isActive = ($index === 0);
            $activeClass = $isActive ? ' active' : '';
            $paneClass = $isActive ? 'tab-pane fade show active' : 'tab-pane fade';

            $tabNavHtml .= '
                <li class="nav-item">
                    <button class="nav-link' . $activeClass . '" data-bs-toggle="tab" data-bs-target="#' . $tab['id'] . '">' . $tab['title'] . '</button>
                </li>';

            $tabContentHtml .= '
                <div class="' . $paneClass . '" id="' . $tab['id'] . '">' . $tab['content'] . '</div>';
        }

        $activity_travel_guides = '
            <section class="travel_guides components mx-auto">
                <h2 class="green-title text-center">Essential <span class="orange-text">Travel Guides</span> for Tour in Nepal</h2><br>
                <ul class="nav nav-underline">' . $tabNavHtml . '
                </ul>

                <div class="tab-content">' . $tabContentHtml . '
                </div>
            </section>
        ';
    }
}


$jVars['module:activity-bread'] = $activity_hero;
$jVars['module:activity-package-cards'] = $activity_package_cards;
$jVars['module:activity-travel-guides'] = $activity_travel_guides;

/*
* Home Activities
*/
$homeActivities = '';
if (defined('HOME_PAGE')) {
    $actRec = Activities::homepageActivities();
    if ($actRec) {
        $homeActivities .= '
        <section class="activities mt-5 text-center">
            <h4>Holiday Types</h4>
            <h2 class="green-title mb-5 mx-auto text-center">
                Find the Perfect <span class="orange-text">Holiday</span> for Every
                <span class="orange-text">Travel Style</span>
            </h2>

            <div class="activities_container container px-3 px-md-5">
                <ul class="card-container d-flex gap-5">';
        
        foreach ($actRec as $actRow) {
            $file_path = SITE_ROOT . 'images/activities/' . $actRow->image;
            $img = (file_exists($file_path) && !empty($actRow->image)) ? IMAGE_PATH . 'activities/' . $actRow->image : BASE_URL . 'template/web/assets/images/tour.png';
            
            $packageCount = Package::get_total_activities_packages($actRow->id);

            $homeActivities .= '
                <li class="custom-card">
                    <img src="' . $img . '" alt="' . $actRow->title . '" />
                    <div class="activities_overlay"></div>
                    <span class="badge-label">
                        <span class="badge-text">' . $packageCount . ' ' . $actRow->title . '</span>
                    </span>
                    <div class="activity_card_content">
                        <h2 class="text-center">' . (!empty($actRow->title_brief) ? $actRow->title_brief : 'Chase the Thrill.') . '</h2>
                        <p>' . strip_tags($actRow->brief) . '</p>

                        <div class="activity_btn">
                            <a href="' . BASE_URL . 'activity/' . $actRow->slug . '" class="explore_btn inquiry-btn normal_button mx-auto">
                                <p>View Trip</p>
                            </a>
                        </div>
                    </div>
                </li>';
        }

        $homeActivities .= '
                </ul>
            </div>
        </section>';
    }
}
$jVars['module:home-activities'] = $homeActivities;
?>