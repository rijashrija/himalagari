<?php
/*
 * Destination list for home section
 */
$resdest = '';
$bread = '';
$heroTitle = '';
$content = '';

$destinationPackageCards = '';
$destRec = Destination::getDestinationlist(4);
if ($destRec) {
    foreach ($destRec as $destRow) {
        $resdest .= '<div class="box_first">
	    	<img src="' . IMAGE_PATH . 'destination/' . $destRow->image . '" alt="' . $destRow->title . '">
    		<a href="' . BASE_URL . 'destination/' . $destRow->slug . '">
    			<span>' . $destRow->title . '</span>
    		</a>
	    </div>';
    }
}

$jVars['module:destination-list'] = $resdest;

/*
 * Destination list for footer section
 */
$resdestfooter = '';
$destRec = Destination::getfooterDestination(4);
if ($destRec) {
    foreach ($destRec as $destRow) {
        $resdestfooter .= '<li>
		    	<a href="' . BASE_URL . 'destination/' . $destRow->slug . '">' . $destRow->title . '</a>
		    </li>';
    }
}

$jVars['module:footer-destination'] = $resdestfooter;

//pr($resdestfooter);
$resdestbdc = $resdestbanner = $resdest = $resdestactv = '';
if (defined('DESTINATION_PAGE') and !empty($_REQUEST['slug'])) {
    $slug = addslashes($_REQUEST['slug']);
    $recRow = Destination::find_by_slug($slug);
    if ($recRow) {

        // Breadcrumb section
        $imgNm = '';
        if ($recRow->gallery != "a:0:{}" and !empty($recRow->gallery)) {
            $imageList = unserialize($recRow->gallery);
            $imgno = array_rand($imageList);
            $file_path = SITE_ROOT . 'images/destination/gallery/' . $imageList[$imgno];
            if (file_exists($file_path)) {
                $imgNm .= IMAGE_PATH . 'destination/gallery/' . $imageList[$imgno];
            } else {
                $imgNm = IMAGE_PATH . 'static/destination-img.jpg';
            }
        } else {
            $imgNm = IMAGE_PATH . 'static/destination-img.jpg';
        }
        $resdestbdc .= '<style>div.inner_banner{background:url(' . $imgNm . ') no-repeat center center;}</style>';

        $resdestbdc .= '<h2><span>' . $recRow->title . '</span></h2>
		<ul>
		    <li><a href="' . BASE_URL . 'home">Home</a></li>
		    <li><i class="fa fa-angle-right" aria-hidden="true"></i></li>
		    <li><a href="javascript:void(0);">Destination</a></li>
		    <li><i class="fa fa-angle-right" aria-hidden="true"></i></li>
		    <li><a href="javascript:void(0);">' . $recRow->title . '</a></li>
		</ul>';

        // Content section
        $resdest .= '';
        $art_img = SITE_ROOT . 'images/destination/' . $recRow->image;
        if (!empty($recRow->image) and file_exists($art_img)) {
            $resdest .= '<div class="col-sm-6 pull-right">
					<img class="img-responsive img-full" src="' . IMAGE_PATH . 'destination/' . $recRow->image . '" alt="' . $recRow->title . '">
				</div>';
        }
        $resdest .= '<div class=""> 
				' . $recRow->content . '
			</div>
		</div>';

        // Sidebar Related activities list
        $actRec = Activities::get_actitvityby($recRow->id, 0);
        if ($actRec) {
            $resdestactv .= '<section>
		        <div class="rows pad-bot-redu">
		            <div class="container">
		                <!-- TITLE & DESCRIPTION -->
		                <div class="spe-title">
		                    <h2> <span>All</span> Activities</h2>
		                    <div class="title-line">
		                        <div class="tl-1"></div>
		                        <div class="tl-2"></div>
		                        <div class="tl-3"></div>
		                    </div>
		                    <p>' . $recRow->title_brief . '</p>
		                </div>
		                <div>';
            foreach ($actRec as $actRow) {
                $resdestactv .= '<div class="col-md-3 col-sm-6 col-xs-12 b_packages">
								<a href="' . BASE_URL . 'activities/' . $recRow->slug . '/' . $actRow->slug . '"><div class="v_place_img">
									<img src="' . IMAGE_PATH . 'activities/' . $actRow->image . '" alt="' . $actRow->title . '">
									<h4>' . $actRow->title . '</h4>
								</div></a>
								<!--<div class="b_pack rows">
									<div class="col-md-12 col-sm-12">
										<h4>' . $actRow->title . '</h4>
										<a href="' . BASE_URL . 'activities/' . $recRow->slug . '/' . $actRow->slug . '" class="link-btn hom-hot-view-btn pull-right">Details</a>
									</div>
								</div>-->
							</div>';
            }

            $resdestactv .= '</div>
		            </div>
		        </div>
		    </section>';
        } else {
            $packageRec = Package::get_filterpkg_by($recRow->id);
            if ($packageRec) {
                $resdestactv .= '<section>
			        <div class="rows pad-bot-redu tb-space">
			            <div class="container">
			                <!-- TITLE & DESCRIPTION -->
			                <div class="spe-title">
			                    <h2> <span>' . $recRow->title . '</span> Packages</h2>
			                    <div class="title-line">
			                        <div class="tl-1"></div>
			                        <div class="tl-2"></div>
			                        <div class="tl-3"></div>
			                    </div>
			                    <p>' . $recRow->title_brief . '</p>
			                </div>
			                <div>';
                foreach ($packageRec as $packageRow) {
                    $file_path = SITE_ROOT . 'images/package/' . $packageRow->image;
                    if (file_exists($file_path) and !empty($packageRow->image)) {
                        $activity = Activities::field_by_id($packageRow->activityId, 'title');
                        $resdestactv .= '<div class="col-md-4">
							            <div class="to-ho-hotel-con">
							                <div class="to-ho-hotel-con-1">
							                    <div class="hom-hot-av-tic">
							                        ' . $activity . ' 
							                    </div>
							                    <img src="' . IMAGE_PATH . 'package/' . $packageRow->image . '" alt="' . $packageRow->title . '">
							                </div>
							                <div class="to-ho-hotel-con-23">
							                    <div class="to-ho-hotel-con-2">
							                        <h4>' . $packageRow->title . '</h4>
							                    </div>
							                    <div class="to-ho-hotel-con-3">
							                        <ul>
							                            <li>
							                                Duration : ' . $packageRow->days . ' Days <br />
							                                Gread : ' . $packageRow->gread . '
							                            </li>
							                            <li><span class="ho-hot-pri">' . $packageRow->currency . ' ' . $packageRow->price . '</span>
							                            </li>
							                        </ul>
							                    </div>
							                    <div class="to-ho-hotel-con-4">
							                        <a href="' . BASE_URL . 'book/package/' . $packageRow->slug . '" class="link-btn hom-hot-book-btn">Book Now</a>
							                        <a href="' . BASE_URL . 'package/' . $packageRow->slug . '" class="link-btn hom-hot-view-btn">View More</a>
							                    </div>
							                </div>
							            </div>
							        </div>';
                    }
                }

                $resdestactv .= '</div>
			            </div>
			        </div>
			    </section>';
            }
        }

    } else {
        $url = BASE_URL . 'pages/errors';
        redirect_to($url);
    }
}

$jVars['module:destination-breadcrumb'] = $resdestbdc;
$jVars['module:destination-details'] = $resdest;
$jVars['module:destination-activities'] = $resdestactv;


// Destination listing
$dest_bread = $dest_list = '';
$destinations = Destination::find_all();
if (defined('DESTINATION_PAGE')) {
    if ($destinations) {
        $dest_bread .= '
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="' . BASE_URL . 'home"><i class="fas fa-home"></i></a></li>
                    <li class="breadcrumb-item active"><a href="#">Destination</a></li>
                </ol>
            </nav>
        ';

        foreach ($destinations as $destination) {
            $img = '';
            /*
            $images = unserialize($destination->gallery);
            if(is_array($images) and !empty($images)){
                $file_path = SITE_ROOT.'images/destination/gallery/'.$images[0];
                if(file_exists($file_path)){
                    $img = '
                        <div class="image">
                            <img src="'.IMAGE_PATH.'destination/gallery/'.$images[0].'" alt="'.$destination->title.'" style="height:450px"/>
                        </div>
                    ';
                }
            }
            */
            $file_path = SITE_ROOT . "images/destination/" . @$destination->image;
            $img = (!empty($destination->image) and file_exists($file_path)) ? IMAGE_PATH . "destination/" . $destination->image : IMAGE_PATH . "static/home-destination.jpg";
            $content = explode('<hr id="system_readmore" style="border-style: dashed; border-color: orange;" />', $destination->content);
            $dest_list .= '
                <div class="col">
                    <figure class="tour-grid-item-01">
                        <a href="' . BASE_URL . 'destination/' . $destination->slug . '">
                            <div class="image">
                                <img src="' . $img . '" alt="' . $destination->title . '" style="height:450px"/>
                            </div>
                            <figcaption class="content ">
                                <h5>' . $destination->title . '</h5>
                                </br>
                                ' . $content[0] . '
                            </figcaption>
                        </a>
                    </figure>
                </div>
            ';
        }
    }
}

$jVars['module:destination-list-breadcrumb'] = $dest_bread;
$jVars['module:destination-list'] = $dest_list;

// Dynamic modules for template/web/destination-list.html

if (defined('DESTINATION_PAGE') and !empty($_REQUEST['slug'])) {
    $slug = addslashes($_REQUEST['slug']);
    $selectedDestination = Destination::find_by_slug($slug);
    if ($selectedDestination) {
        $heroTitle = $selectedDestination->title;
        $content = explode('<hr id="system_readmore" style="border-style: dashed; border-color: orange;" />', $selectedDestination->content);
        if (!empty($selectedDestination->title_brief)) {
            $heroDescription = strip_tags($selectedDestination->title_brief);
        }
        $packageRows = Package::get_filterpkg_by($selectedDestination->id);
    } else {
        $packageRows = Package::getPackage(9);
    }
} else {
    $packageRows = Package::getPackage(9);
}

if ($packageRows) {
        if ($heroTitle) {
        $banner_img = '';
        if (isset($selectedDestination) && $selectedDestination) {
            $gallery_img = '';
            if (!empty($selectedDestination->gallery) && $selectedDestination->gallery != 'a:0:{}') {
                $gallery_array = unserialize($selectedDestination->gallery);
                if (is_array($gallery_array) && !empty($gallery_array)) {
                    $gallery_img = $gallery_array[0]; // get the first image
                }
            }

            if (!empty($gallery_img)) {
                $file_path = SITE_ROOT . 'images/destination/gallery/' . $gallery_img;
                if (file_exists($file_path)) {
                    $banner_img = IMAGE_PATH . 'destination/gallery/' . $gallery_img;
                }
            }
            
            if (empty($banner_img)) {
                $file_path2 = SITE_ROOT . 'images/destination/' . $selectedDestination->image;
                if (!empty($selectedDestination->image) && file_exists($file_path2)) {
                    $banner_img = IMAGE_PATH . 'destination/' . $selectedDestination->image;
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

    $bread .= '<section class="nepal-hero-content nepal mt-4">
            <section class="hero" ' .$hero_bg. ' >
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
        </section>';

        }

    foreach ($packageRows as $packageRow) {
        $file_path = SITE_ROOT . 'images/package/' . $packageRow->image;
        $img = (!empty($packageRow->image) and file_exists($file_path)) ? IMAGE_PATH . 'package/' . $packageRow->image : IMAGE_PATH . 'static/home-destination.jpg';

        $destinationTitle = !empty($packageRow->destinationId) ? Destination::field_by_id((int) $packageRow->destinationId, 'title') : '';
        $activityTitle = !empty($packageRow->activityId) ? Activities::field_by_id((int) $packageRow->activityId, 'title') : '';
        $accomodation = !empty($packageRow->accomodation) ? $packageRow->accomodation : 'N/A';
        $difficulty = !empty($packageRow->difficulty) ? $packageRow->difficulty : 'Moderate';
        $days = !empty($packageRow->days) ? (int) $packageRow->days : 0;

        $destinationTitleClean = trim(strip_tags((string) $destinationTitle));
        $activityTitleClean = trim(strip_tags((string) $activityTitle));
        $accomodationClean = trim(strip_tags((string) $accomodation));
        $difficultyClean = trim(strip_tags((string) $difficulty));

        $destinationTitleDisplay = (strlen($destinationTitleClean) > 32) ? substr($destinationTitleClean, 0, 29) . '...' : $destinationTitleClean;
        $activityTitleDisplay = (strlen($activityTitleClean) > 32) ? substr($activityTitleClean, 0, 29) . '...' : $activityTitleClean;
        $accomodationDisplay = $accomodationClean;
        $difficultyDisplay = $difficultyClean;

        $popularBadge = ($packageRow->popular == 1) ? '<span class="badge">POPULAR</span>' : '';

        $destinationPackageCards .= '<div class="package-card">
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
            $destinationPackageCards .= '
                                    <div class="info">
                                        <span class="label">Activities</span>
                                        <span class="value green" title="' . htmlspecialchars($activityTitleClean, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($activityTitleDisplay, ENT_QUOTES, 'UTF-8') . '</span>
                                    </div>
                                    <div class="row-divider"></div>';
        }
        $destinationPackageCards .= '
                                    <div class="info text-right">
                                        <span class="label">Difficulty-level</span>
                                        <span class="value green" title="' . htmlspecialchars($difficultyClean, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($difficultyDisplay, ENT_QUOTES, 'UTF-8') . '</span>
                                    </div>
                                </div>';

        $destinationPackageCards .= '
                            </div>
                        </div>
                    </div>';
    }
}

if (empty($destinationPackageCards)) {
    $destinationPackageCards = '<div class="col-12 text-center"><p>No packages found for this destination right now.</p></div>';
}


$jVars['module:destination-package-cards'] = $destinationPackageCards;
$jVars['module:destination-bread'] = $bread;
