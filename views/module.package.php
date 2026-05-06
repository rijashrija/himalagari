<?php
/*
* Module Package 
* Package::get_databy_display(column name, status, limit);
*/

// Destination display in homepage
$home_destination = '';
if (defined("HOME_PAGE")) {
    $destinations = Destination::get_destination();
    if (!empty($destinations)) {
        foreach ($destinations as $destination) {
            $file_path = SITE_ROOT . "images/destination/" . @$destination->image;
            $img = (!empty($destination->image) and file_exists($file_path)) ? IMAGE_PATH . "destination/" . $destination->image : IMAGE_PATH . "static/home-destination.jpg";
            $tours = Package::get_total_destination_packages($destination->id);
            $briefs = explode('<hr id="system_readmore" style="border-style: dashed; border-color: orange;" />', $destination->content);
            $home_destination .= '
                <div class="slick-item">
                    <figure class="destination-grid-item-01 hovereffect">
                   
                        <a href="' . BASE_URL . 'destination/' . $destination->slug . '">
                            <div class="image">
                                <img src="' . $img . '" alt="' . $destination->title . '"/>
                            </div>
                            <figcaption class="content tour-slide tour-slide123">
                                <h3>' . $destination->title . '</h3>
                                <p class="text-muted1">' . $tours . ' Packages</p>
                            </figcaption>
                            <div class="overlay">
                                <h4>' . $destination->title . '</h4><br/>
                                <h3>' . $tours . ' Packages</h3>
                                <h3 class="hoverp">' . strip_tags($briefs[0]) . '</h3>
                        </div>
                        </a>
                    </figure>
                </div>
            ';
        }
    }
}
$jVars["module:home-destination"] = $home_destination;

/* Hero Best Seller Card (homepage) */
$home_best_seller = '';
$bestSellerSql = "SELECT p.*, COALESCE(SUM(CASE WHEN b.trip_pax IS NULL OR b.trip_pax = '' THEN 1 ELSE CAST(b.trip_pax AS UNSIGNED) END), 0) AS total_pax
                  FROM tbl_package p
                  LEFT JOIN tbl_bookinginfo b ON b.pkg_id = p.id
                  WHERE p.status = 1 AND p.popular = 1
                  GROUP BY p.id
                  ORDER BY total_pax DESC, p.sortorder ASC
                  LIMIT 1";
$bestSellerQuery = $db->query($bestSellerSql);
$bestSellerPkg = ($db->num_rows($bestSellerQuery) > 0) ? $db->fetch_object($bestSellerQuery) : null;
$bestSellerTotalTrekkers = 0;

if (!empty($bestSellerPkg) && isset($bestSellerPkg->total_pax)) {
  $bestSellerTotalTrekkers = (int)$bestSellerPkg->total_pax;
}

if (empty($bestSellerPkg)) {
    $popularFallback = Package::get_databy_display('popular', 1, 1);
    if (!empty($popularFallback)) {
        $bestSellerPkg = $popularFallback[0];
    }
}

if (!empty($bestSellerPkg) && !empty($bestSellerPkg->slug)) {
    $bestTitle = htmlspecialchars($bestSellerPkg->title, ENT_QUOTES, 'UTF-8');
    $bestDays = !empty($bestSellerPkg->days) ? (int)$bestSellerPkg->days : 0;
    $destinationTitle = !empty($bestSellerPkg->destinationId) ? Destination::field_by_id($bestSellerPkg->destinationId, 'title') : 'Nepal';
    $destinationTitle = htmlspecialchars($destinationTitle, ENT_QUOTES, 'UTF-8');
    $difficultyText = !empty($bestSellerPkg->difficulty) ? $bestSellerPkg->difficulty : 'Moderate';
    $difficultyText = htmlspecialchars($difficultyText, ENT_QUOTES, 'UTF-8');
    $seasonText = !empty($bestSellerPkg->season) ? $bestSellerPkg->season : 'All Season';
    $seasonText = htmlspecialchars($seasonText, ENT_QUOTES, 'UTF-8');
    $totalTrekkers = $bestSellerTotalTrekkers;
    $trekkerLine = ($totalTrekkers > 0)
        ? '🔥 ' . number_format($totalTrekkers) . '+ trekkers booked'
        : '🔥 Be first to book this adventure';

    $subtitle = ($bestDays > 0 ? $bestDays . ' Days' : 'Custom Duration') . ' • ' . $destinationTitle;

    $home_best_seller = '
      <div class="best-seller-card">
        <span class="bs-badge">Best Seller</span>

        <a href="' . BASE_URL . 'package/' . $bestSellerPkg->slug . '">
          <h2 class="bs-title">' . $bestTitle . '</h2>
        </a>

        <p class="bs-subtitle">' . $subtitle . '</p>

        <div class="bs-info">
          <p>' . $trekkerLine . '</p>
          <p><strong>Difficulty Level : </strong> ' . $difficultyText . '</p>
          <p><strong>Best Season : </strong> ' . $seasonText . '</p>
        </div>

        <a href="' . BASE_URL . 'package/' . $bestSellerPkg->slug . '" class="explore_btn normal_button inquiry-btn mx-auto">
          <p>View Packages</p>
        </a>
      </div>';
}

$jVars['module:home-best-seller'] = $home_best_seller;

/* Package Display Using Home Flag */
$reshome = $grade = $destination_name = $rating = '';
$homeRec = Package::get_databy_display('popular', 1, 100);

if (!empty($homeRec)) {
    $reshome .= '
    <section class="packages-wrapper components">
      <header class="page-header">
        <h4 class="text-center">Popular Packages</h4>
        <h2 class="green-title text-center">
          Handpicked <span class="orange-text">Travel</span> Itineraries
        </h2>
      </header>
      <br />
      <div class="package-grid-wrapper">
        <div class="packages-grid mx-auto">';
    
    $homeCount = 0;
    foreach ($homeRec as $RecRow) {
        $homeCount++;
        $img = '';
        // getting image
        $file_path = SITE_ROOT . "images/package/" . @$RecRow->image;
        $img = (!empty($RecRow->image) and file_exists($file_path)) ? IMAGE_PATH . "package/" . $RecRow->image : IMAGE_PATH . "static/home-featured.jpg";
        
        // getting details
        $destination_name = !empty($RecRow->destinationId) ? Destination::field_by_id($RecRow->destinationId, 'title') : '';
        $activityTitle = !empty($RecRow->activityId) ? Activities::field_by_id($RecRow->activityId, 'title') : '';
        $accomodation = !empty($RecRow->accomodation) ? $RecRow->accomodation : 'N/A';
        $difficulty = !empty($RecRow->difficulty) ? $RecRow->difficulty : 'Moderate';
        
        // Popular Badge
        $popularBadge = ($RecRow->popular == 1) ? '<span class="badge">POPULAR</span>' : '';
        $hideHomeStyle = ($homeCount > 6) ? ' style="display:none;"' : '';

        $reshome .= '
        <div class="package-card"'. $hideHomeStyle .'>
          <div class="image-wrapper">
            <a href="' . BASE_URL . 'package/' . $RecRow->slug . '">
              <img src="' . $img . '" alt="' . htmlspecialchars($RecRow->title, ENT_QUOTES, 'UTF-8') . '" />
            </a>
            ' . $popularBadge . '
          </div>

          <div class="card-content">
            <a href="' . BASE_URL . 'package/' . $RecRow->slug . '">
              <h3 class="title">' . htmlspecialchars($RecRow->title, ENT_QUOTES, 'UTF-8') . '</h3>
            </a>

            <div class="card-info-wrapper">

              <div class="info-row">
                <div class="info">
                  <span class="label">Destination</span>
                  <span class="value green" title="' . htmlspecialchars($destination_name, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($destination_name, ENT_QUOTES, 'UTF-8') . '</span>
                </div>
                <div class="row-divider"></div>
                <div class="info text-right">
                  <span class="label">Accomodations</span>
                  <span class="value green" title="' . htmlspecialchars($accomodation, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($accomodation, ENT_QUOTES, 'UTF-8') . '</span>
                </div>
              </div>

              <div class="action-row">
                <span class="days-badge">' . $RecRow->days . ' days</span>
                <a href="' . BASE_URL . 'package/' . $RecRow->slug . '" class="explore_btn">
                  <p>Explore</p>
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                  </svg>
                </a>
              </div>

              <div class="info-row">
                <div class="info">
                  <span class="label">Activities</span>
                  <span class="value green" title="' . htmlspecialchars($activityTitle, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($activityTitle, ENT_QUOTES, 'UTF-8') . '</span>
                </div>
                <div class="row-divider"></div>
                <div class="info text-right">
                  <span class="label">Difficulty-level</span>
                  <span class="value green" title="' . htmlspecialchars($difficulty, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($difficulty, ENT_QUOTES, 'UTF-8') . '</span>
                </div>
              </div>

            </div>
          </div>
        </div>';
    }

    $reshome .= '
      </div>';
    if (count($homeRec) > 6) {
        $reshome .= '
      <div class="load-more-wrap text-center mt-4">
        <button type="button" class="explore_btn mx-auto inquiry-btn js-load-more-packages" data-initial="6" data-step="24">
          <p>Load More Packages</p>
        </button>
      </div>';
    }
    $reshome .= '
      </div>
    </section>';
}

$jVars['module:package-home'] = $reshome;

/* Top-Rated Family Packages — shown on homepage */
$res_toprated = '';
$topRatedRec = Package::get_top_rated_packages(1, 100);

if (!empty($topRatedRec)) {
    $res_toprated .= '
    <section class="packages-wrapper components">
      <header class="page-header">
        <h4 class="text-center">Highly Recommended</h4>
        <h2 class="green-title text-center">
          Discover our <span class="orange-text">Top Rated</span> Adventures
        </h2>
      </header>
      <br />
      <div class="packages-grid mx-auto">';

    $trCount = 0;
    foreach ($topRatedRec as $pkg) {
        $trCount++;
        $file_path = SITE_ROOT . 'images/package/' . @$pkg->image;
        $img = (!empty($pkg->image) && file_exists($file_path))
            ? IMAGE_PATH . 'package/' . $pkg->image
            : IMAGE_PATH . 'static/home-featured.jpg';

        $destination_name = !empty($pkg->destinationId)
            ? Destination::field_by_id($pkg->destinationId, 'title') : 'Nepal';
        $activityTitle = !empty($pkg->activityId)
            ? Activities::field_by_id($pkg->activityId, 'title') : '';
        $accomodation  = !empty($pkg->accomodation) ? $pkg->accomodation : 'N/A';
        $difficulty    = !empty($pkg->difficulty)   ? $pkg->difficulty   : 'Moderate';

        // Average rating — already available as avg_rating on the object via SQL alias
        $avg = isset($pkg->avg_rating) ? round((float)$pkg->avg_rating, 1) : 0;
        $stars = '';
        for ($s = 1; $s <= 5; $s++) {
            $stars .= ($s <= floor($avg))
                ? '<i class="fa fa-star" style="color:#f5a623;font-size:12px;"></i>'
                : '<i class="fa fa-star-o" style="color:#ccc;font-size:12px;"></i>';
        }

        $popularBadge = '<span class="badge" style="background:var(--green);">TOP RATED</span>';

        $hideStyle = ($trCount > 6) ? ' style="display:none;"' : '';

        $res_toprated .= '
        <div class="package-card"'. $hideStyle .'>
          <div class="image-wrapper">
            <a href="' . BASE_URL . 'package/' . $pkg->slug . '">
              <img src="' . $img . '" alt="' . htmlspecialchars($pkg->title, ENT_QUOTES, 'UTF-8') . '" />
            </a>
            ' . $popularBadge . '
          </div>
          <div class="card-content">
            <a href="' . BASE_URL . 'package/' . $pkg->slug . '">
              <h3 class="title">' . htmlspecialchars($pkg->title, ENT_QUOTES, 'UTF-8') . '</h3>
            </a>
            <div class="card-info-wrapper">

              <div class="info-row">
                <div class="info">
                  <span class="label">Destination</span>
                  <span class="value green" title="' . htmlspecialchars($destination_name, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($destination_name, ENT_QUOTES, 'UTF-8') . '</span>
                </div>
                <div class="row-divider"></div>
                <div class="info text-right">
                  <span class="label">Accomodations</span>
                  <span class="value green" title="' . htmlspecialchars($accomodation, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($accomodation, ENT_QUOTES, 'UTF-8') . '</span>
                </div>
              </div>

              <div class="action-row">
                <span class="days-badge">' . $pkg->days . ' days</span>
                <a href="' . BASE_URL . 'package/' . $pkg->slug . '" class="explore_btn">
                  <p>Explore</p>
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                  </svg>
                </a>
              </div>

              <div class="info-row">
                <div class="info">
                  <span class="label">Activities</span>
                  <span class="value green" title="' . htmlspecialchars($activityTitle, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($activityTitle, ENT_QUOTES, 'UTF-8') . '</span>
                </div>
                <div class="row-divider"></div>
                <div class="info text-right">
                  <span class="label">Difficulty-level</span>
                  <span class="value green" title="' . htmlspecialchars($difficulty, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($difficulty, ENT_QUOTES, 'UTF-8') . '</span>
                </div>
              </div>
              
            </div>
          </div>
        </div>';
    }

    $res_toprated .= '
      </div>';
    if (count($topRatedRec) > 6) {
        $res_toprated .= '
      <div class="load-more-wrap text-center mt-4">
        <button type="button" class="explore_btn inquiry-btn js-load-more-packages" data-initial="6" data-step="24">
          <p>Load More Packages</p>
        </button>
      </div>';
    }
    $res_toprated .= '
    </section>';
} else {
    // Fallback: show section with popular packages if no rated ones exist
    $fallbackRec = Package::get_databy_display('popular', 1, 100);
    if (!empty($fallbackRec)) {
        $res_toprated .= '
    <section class="packages-wrapper components">
      <header class="page-header">
        <h4 class="text-center">Highly Recommended</h4>
        <h2 class="green-title">
          Discover our <span class="orange-text">Top Rated</span> Adventures
        </h2>
      </header>
      <br />
      <div class="packages-grid mx-auto">';
        $fbCount = 0;
        foreach ($fallbackRec as $pkg) {
            $fbCount++;
            $file_path = SITE_ROOT . 'images/package/' . @$pkg->image;
            $img = (!empty($pkg->image) && file_exists($file_path))
                ? IMAGE_PATH . 'package/' . $pkg->image
                : IMAGE_PATH . 'static/home-featured.jpg';
            $destination_name = !empty($pkg->destinationId)
                ? Destination::field_by_id($pkg->destinationId, 'title') : 'Nepal';
            $activityTitle = !empty($pkg->activityId)
                ? Activities::field_by_id($pkg->activityId, 'title') : '';
            $accomodation = !empty($pkg->accomodation) ? $pkg->accomodation : 'N/A';
            $difficulty   = !empty($pkg->difficulty)   ? $pkg->difficulty   : 'Moderate';
            $hideStyle = ($fbCount > 6) ? ' style="display:none;"' : '';
            $res_toprated .= '
        <div class="package-card"'. $hideStyle .'>
          <div class="image-wrapper">
            <a href="' . BASE_URL . 'package/' . $pkg->slug . '">
              <img src="' . $img . '" alt="' . htmlspecialchars($pkg->title, ENT_QUOTES, 'UTF-8') . '" />
            </a>
            <span class="badge">POPULAR</span>
          </div>
          <div class="card-content">
            <a href="' . BASE_URL . 'package/' . $pkg->slug . '">
              <h3 class="title">' . htmlspecialchars($pkg->title, ENT_QUOTES, 'UTF-8') . '</h3>
            </a>
            <div class="card-info-wrapper">

              <div class="info-row">
                <div class="info">
                  <span class="label">Destination</span>
                  <span class="value green" title="' . htmlspecialchars($destination_name, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($destination_name, ENT_QUOTES, 'UTF-8') . '</span>
                </div>
                <div class="row-divider"></div>
                <div class="info text-right">
                  <span class="label">Accomodations</span>
                  <span class="value green" title="' . htmlspecialchars($accomodation, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($accomodation, ENT_QUOTES, 'UTF-8') . '</span>
                </div>
              </div>

              <div class="action-row">
                <span class="days-badge">' . $pkg->days . ' days</span>
                <a href="' . BASE_URL . 'package/' . $pkg->slug . '" class="explore_btn">
                  <p>Explore</p>
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                  </svg>
                </a>
              </div>

              <div class="info-row">
                <div class="info">
                  <span class="label">Activities</span>
                  <span class="value green" title="' . htmlspecialchars($activityTitle, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($activityTitle, ENT_QUOTES, 'UTF-8') . '</span>
                </div>
                <div class="row-divider"></div>
                <div class="info text-right">
                  <span class="label">Difficulty-level</span>
                  <span class="value green" title="' . htmlspecialchars($difficulty, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($difficulty, ENT_QUOTES, 'UTF-8') . '</span>
                </div>
              </div>

            </div>
          </div>
        </div>';
        }
        $res_toprated .= '
      </div>';
    if (count($fallbackRec) > 6) {
        $res_toprated .= '
      <div class="load-more-wrap text-center mt-4">
        <button type="button" class="explore_btn inquiry-btn mx-auto js-load-more-packages" data-initial="6" data-step="24">
          <p>Load More Packages</p>
        </button>
      </div>';
    }
    $res_toprated .= '
    </section>';
    }
}

$jVars['module:top-rated-packages'] = $res_toprated;

/* Package Display Using Feature Flag */
$resfeature = '';

$featureRec = Package::get_databy_display('featured', 1, 6);

if (!empty($featureRec)) {
    foreach ($featureRec as $fetRow) {
        $img = $tag = '';
        // getting image
        $file_path = SITE_ROOT . "images/package/" . $fetRow->image;
        $img = (!empty($fetRow->image) and file_exists($file_path)) ? IMAGE_PATH . "package/" . $fetRow->image : $img = IMAGE_PATH . "static/home-featured.jpg";

        // getting tags
        $tag = (!empty($fetRow->tags)) ? '<span class="ribbon_3 ' . $fetRow->color . '">' . $fetRow->tags . '</span>' : '';

        // getting destination
        $destination_name = Destination::field_by_id($fetRow->destinationId, 'title');

        // getting avg rating
        $rating = Package::get_avg_rating($fetRow->id);

        $price_text = '';
        if(!empty($fetRow->price) and (empty($fetRow->offer_price))){$price_text = '<p class="home-price">Starting USD '.$fetRow->price.'</p>';}
        if(!empty($fetRow->offer_price)){$price_text = '<p class="home-price">Starting USD <del>'.$fetRow->price.'</del> '.$fetRow->offer_price.'</p>';}
        
        $resfeature .= '
            <div class="col">
                <figure class="tour-grid-item-01">
                    <a href="' . BASE_URL . 'package/' . $fetRow->slug . '">
                        <style>
                            .hgt-230{height:230px !important;}
                            .new-img3.tt{top:54%;}
                        </style>
                        <div class="image">
                            <img src="' . $img . '" alt="' . $fetRow->title . '" class="hgt-230"/>
                            '.$price_text.'
                            
                            
                        </div>
                        <figcaption class="content ">
                            ' . $tag . '
                            <h5 class="">' . $fetRow->title . '</h5>
                            <ul class="item-meta mt-15">
                                <li>
                                    <!--<i class="elegent-icon-pin_alt text-warning"></i>--> 
                                    <i class="far fa-map pr-2"></i>' . $destination_name . '
                                </li>
                                <!--<li>
                                    <div class="rating-item rating-sm rating-inline clearfix">
                                        <div class="rating-icons">
                                            <input type="hidden" class="rating"
                                                   data-filled="rating-icon ri-star rating-rated"
                                                   data-empty="rating-icon ri-star-empty" data-fractions="2"
                                                   data-readonly value="' . $rating . '"/>
                                        </div>
                                    </div>
                                </li>-->
                                <li><span class="font700 h6"><i class="far fa-hourglass"></i>' . $fetRow->days . ' Days</span></li>
                            </ul>
                            <!-- <ul class="item-meta mt-15">
                                <li><span class="font700 h6">' . $fetRow->days . ' Days</span></li>
                                <li>
                                    <p class="mt-3">Price from <span class="h6 line-1 text-primary font16">$ ' . $fetRow->price . '</span>
                                        <span class="text-muted mr-5"></span></p>
                                </li>
                            </ul>-->
                        </figcaption>';
        if (!empty($fetRow->accomodation)) {
            $resfeature .= '<p class="featured-trip1">';
            $routes = explode(',', $fetRow->accomodation);
            foreach ($routes as $route) {
                $resfeature .= (end($routes) == $route) ? $route : $route . ' -> ';
            }
            $resfeature .= '</p>';
        }
        if (!empty($fetRow->difficulty)) {
            switch ($fetRow->difficulty) {
                case 'Easy':
                    $resfeature .= '<img src="' . IMAGE_PATH . 'static/meter/1.png" class="new-img3 tt" title="'.$fetRow->difficulty.'" alt="Difficulty">';
                    break;
                case 'Moderate':
                    $resfeature .= '<img src="' . IMAGE_PATH . 'static/meter/2.png" class="new-img3 tt" title="'.$fetRow->difficulty.'" alt="Difficulty">';
                    break;
                case 'Moderate To Strenous':
                    $resfeature .= '<img src="' . IMAGE_PATH . 'static/meter/3.png" class="new-img3 tt" title="'.$fetRow->difficulty.'" alt="Difficulty">';
                    break;
                case 'Strenous':
                    $resfeature .= '<img src="' . IMAGE_PATH . 'static/meter/4.png" class="new-img3 tt" title="'.$fetRow->difficulty.'" alt="Difficulty">';
                    break;
                case 'Very Strenous':
                    $resfeature .= '<img src="' . IMAGE_PATH . 'static/meter/5.png" class="new-img3 tt" title="'.$fetRow->difficulty.'" alt="Difficulty">';
                    break;
            }
        }
        $resfeature .= '    
                    </a>
                </figure>
            </div>
        ';
    }
}

$jVars['module:package-feature'] = $resfeature;

/* package display using Fixed flag */
$resfix = '';
$sql = "SELECT MAX(pd.package_currency),MAX(pd.package_rate), MIN(pd.package_date) AS package_date, p.slug, p.title, p.difficulty, p.image, p.tags, p.breif, p.days, p.gread, p.pdate, p.destinationId, p.activityId FROM tbl_package_date AS pd 
INNER JOIN tbl_package AS p ON pd.package_id = p.id 
WHERE 
p.status='1' AND pd.status='1' AND package_date>=CURDATE() GROUP BY pd.package_id ORDER BY package_date ASC ";
$query = $db->query($sql);
$totl = $db->num_rows($query);
if ($totl > 0) {
    while ($FixRow = $db->fetch_object($query)) {
        $file_path = SITE_ROOT . 'images/package/' . $FixRow->image;
        if (file_exists($file_path) and !empty($FixRow->title)) {
            $activity = Activities::field_by_id($FixRow->activityId, 'title');
            $resfix .= '<div class="col-md-4">
            <div class="to-ho-hotel-con"><a href="' . BASE_URL . 'package/' . $FixRow->slug . '">
                <div class="to-ho-hotel-con-1">
                    <div class="hom-hot-av-tic">
                        ' . $activity . ' 
                    </div>
                    <img src="' . IMAGE_PATH . 'package/' . $FixRow->image . '" alt="' . $FixRow->title . '">
                </div>
                <div class="to-ho-hotel-con-23">
                    <div class="to-ho-hotel-con-2">
                        <h4>' . $FixRow->title . '</h4>
                    </div></a>
                    <div class="to-ho-hotel-con-3">
                        <ul>
                            <li>
                                Duration : ' . $FixRow->days . ' <br />
                                Difficulty : ' . set_na($FixRow->difficulty) . '
                            </li>
                        </ul>
                    </div>
                    <div class="to-ho-hotel-con-4">
                        <a href="' . BASE_URL . 'book/package/' . $FixRow->slug . '" class="link-btn hom-hot-book-btn">Book Now</a>
                        <a href="' . BASE_URL . 'package/' . $FixRow->slug . '" class="link-btn hom-hot-view-btn">View More</a>
                    </div>
                </div>
            </div>
        </div>';
        }
    }
}

$jVars['module:package-fixed'] = $resfix;


/*fixed departure above testimonial
*/
$fixedlist = '';
$fixRec = Package::get_databy_display('fixed', 1, 18);
if (!empty($fixRec)) {
    foreach ($fixRec as $FixRow) {
        // echo "<pre>";print_r($FixRow);
        // echo $FixRow->title;
        $file_path = SITE_ROOT . 'images/package/' . $FixRow->image;
        if (file_exists($file_path) and !empty($FixRow->title)) {
            if (!empty($FixRow->date)) {
                $fixedlist .= '<div class="dpt"><a class="link" href="' . BASE_URL . 'package/' . $FixRow->slug . '">
            <div class="row">';
                $fixedlist .= '<div class="col-xs-4 date">' . $FixRow->date . '</div>
                <div class="col-xs-8 title">' . $FixRow->title . '</div>';
                $fixedlist .= ' </div></a></div>';
            }
        }
    }
}

$jVars['module:dept-fixed'] = $fixedlist;


// sidebar feature package
$respkg_feature = '';

$featureRec = Package::get_databy_display('featured', 1, 6);
if (!empty($featureRec)) {
    foreach ($featureRec as $pkgfRow) {
        $file_path = SITE_ROOT . 'images/package/' . $pkgfRow->image;
        if (file_exists($file_path) and !empty($pkgfRow->image)) {
            $respkg_feature .= '<div class="hotel-small style-2 clearfix">
                <a class="hotel-img black-hover" href="' . BASE_URL . 'package/' . $pkgfRow->slug . '">
                    <img class="img-responsive radius-0" src="' . IMAGE_PATH . 'package/' . $pkgfRow->image . '" alt="' . $pkgfRow->title . '">
                    <div class="tour-layer delay-1"></div>                              
                </a>
                <div class="hotel-desc">
                    <h4><a href="' . BASE_URL . 'package/' . $pkgfRow->slug . '">' . $pkgfRow->title . '</a></h4>
                    <div class="tour-info-line">
                        <div class="tour-info">
                            <img src="' . BASE_URL . 'template/web/img/calendar_icon_grey.png" alt="Image not Found">
                            <span class="font-style-2 color-dark-2">' . $pkgfRow->days . '</span>
                        </div>                  
                    </div>                                                                          
                </div>
            </div>';
        }
    }
}

$jVars['module:package-featureside'] = $respkg_feature;



// Package Detail section 
$respkg_breadcrumb = $respkg_detail = $send_review = $email_friend = $ask_question = '';
if (defined('PACKAGE_PAGE')) {
    $slug = (isset($_REQUEST['slug']) and !empty($_REQUEST['slug'])) ? addslashes($_REQUEST['slug']) : '';
    $pkgRec = Package::find_by_slug($slug);

    $respkg_detail .= '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';

    $destslug = Destination::field_by_id($pkgRec->destinationId, 'slug');

    // getting avg rating
    $rating = Package::get_avg_rating($pkgRec->id);
    $reviews_total = Package::get_review_num($pkgRec->id);

    if (!empty($pkgRec)) {
        
        $destslug = Destination::field_by_id($pkgRec->destinationId, 'slug');
        $rating = Package::get_avg_rating($pkgRec->id);
        $reviews_total = Package::get_review_num($pkgRec->id);
        
        $respkg_detail .= '
        <div class="inner_package_container mt-5">
            <div class="header">
                <h2 class="green-title text-center">' . $pkgRec->title . '</h2>
                <div class="rating">
                    <div class="stars">';
        for($i=1; $i<=5; $i++){
            $respkg_detail .= ($i <= $rating) ? '★' : '☆';
        }
        $respkg_detail .= '</div>
                    <span class="score">' . $rating . '</span>
                    <span class="reviews">Based on ' . $reviews_total . ' Reviews</span>
                </div>
            </div>
        ';

        // Gallery
        $sliderImages = PackageImage::getImagelist_by($pkgRec->id);
        if ($sliderImages) {
            $mainImg = $sliderImages[0]->image;
            $respkg_detail .= '
            <div class="gallery mb-5">
                <div class="main-image">
                    <img src="' . IMAGE_PATH . 'package/galleryimages/' . $mainImg . '" alt="' . $pkgRec->title . '" class="gallery-img" />
                    <button class="view-more-btn">View all photos</button>
                </div>
                <div class="thumbnails">
            ';
            for ($i = 1; $i <= min(3, count($sliderImages) - 1); $i++) {
                $thumbClass = ($i == 3) ? 'thumb view-all' : 'thumb';
                $respkg_detail .= '
                    <div class="' . $thumbClass . '">
                        <img src="' . IMAGE_PATH . 'package/galleryimages/' . $sliderImages[$i]->image . '" alt="" class="gallery-img">
                    </div>
                ';
            }
            $respkg_detail .= '</div>';
            
            $respkg_detail .= '<div style="display:none;" id="hiddenGallery">';
            foreach ($sliderImages as $sliderImage) {
                $respkg_detail .= '<img src="' . IMAGE_PATH . 'package/galleryimages/' . $sliderImage->image . '">';
            }
            $respkg_detail .= '</div></div>';
            
            // Lightbox
            $respkg_detail .= '
              <div class="lightbox" id="lightbox">
                <span class="close">&times;</span>
                <button class="nav prev">&#10094;</button>
                <img class="lightbox-img" id="lightboxImg" />
                <button class="nav next">&#10095;</button>
              </div>
            ';
        }

        $respkg_detail .= '
        <section class="package-section">
          <div class="container">
            <div class="package-left">
              <div class="package-info">
                <h2 class="green-title"><span>About</span> Package</h2>
                ' . preg_replace('/<(h[23])(.*?)>(Highlights)(.*?)<\/\1>/i', '<$1 class="green-title highlights-text"$2><span>$3</span>$4</$1>', $pkgRec->overview) . '
                
                <ul class="features-box">
        ';
        if (!empty($pkgRec->accomodation)) {
            $respkg_detail .= '
                  <li class="feature">
                    <span>🏨</span>
                    <h4>Accommodation</h4>
                    <p>' . $pkgRec->accomodation . '</p>
                  </li>
            ';
        }
        $actTitle = Activities::field_by_id($pkgRec->activityId, 'title');
        if (!empty($actTitle)) {
            $respkg_detail .= '
                  <li class="feature">
                    <span>🥾</span>
                    <h4>Activities</h4>
                    <p>' . $actTitle . '</p>
                  </li>
            ';
        }
        if (!empty($pkgRec->difficulty)) {
            $respkg_detail .= '
                  <li class="feature">
                    <span>⛰️</span>
                    <h4>Difficulty</h4>
                    <p>' . $pkgRec->difficulty . '</p>
                  </li>
            ';
        }
        if (!empty($pkgRec->group_size)) {
            $respkg_detail .= '
                  <li class="feature">
                    <span>👥</span>
                    <h4>Group Size</h4>
                    <p>' . $pkgRec->group_size . ' people</p>
                  </li>
            ';
        }
        if (!empty($pkgRec->days)) {
            $respkg_detail .= '
                  <li class="feature">
                    <span>👣</span>
                    <h4>Trip Duration</h4>
                    <p>' . $pkgRec->days . ' Days</p>
                  </li>
            ';
        }
        if (!empty($pkgRec->season)) {
            $respkg_detail .= '
                  <li class="feature">
                    <span>🌸</span>
                    <h4>Season</h4>
                    <p>' . $pkgRec->season . '</p>
                  </li>
            ';
        }
        $respkg_detail .= '</ul></div>';

        // Itinerary
        $itineraries = Itinerary::getPackage_limit($pkgRec->id);
        if ($itineraries) {
            $respkg_detail .= '
              <section class="itinerary-section mt-5">
                <div class="tour-itinerary-container">
                  <h2 class="green-title section-title"><span>Trip</span> Itinerary</h2>
                  <div class="accordion itinerary-accordion mx-auto" id="accordionExample">
            ';
            $itCount = 1;
            foreach ($itineraries as $itinerary) {
                $isExpanded = ($itCount == 1) ? 'true' : 'false';
                $showClass = ($itCount == 1) ? 'show' : '';
                $collapsedClass = ($itCount == 1) ? '' : 'collapsed';
                
                $respkg_detail .= '
                    <div class="accordion-item">
                      <h2 class="accordion-header">
                        <button class="accordion-button ' . $collapsedClass . '" type="button" data-bs-toggle="collapse" data-bs-target="#day' . $itCount . '"
                          aria-expanded="' . $isExpanded . '" aria-controls="day' . $itCount . '">
                          ' . str_pad($itinerary->day, 2, '0', STR_PAD_LEFT) . ': ' . $itinerary->title . '
                        </button>
                      </h2>
                      <div id="day' . $itCount . '" class="accordion-collapse collapse ' . $showClass . '" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                          ' . $itinerary->content . '
                        </div>
                      </div>
                    </div>
                ';
                $itCount++;
            }
            $respkg_detail .= '</div></div></section>';
        }

        // Map
        if ($pkgRec->maptype == 1 && file_exists(SITE_ROOT . 'images/package/map/' . $pkgRec->mapimage)) {
            $respkg_detail .= '
              <section class="route-map-section mt-5">
                <div class="package_destination_map">
                  <h2 class="green-title section-title"><span>Package</span> Route Map</h2>
                  <img src="' . IMAGE_PATH . 'package/map/' . $pkgRec->mapimage . '" alt="routes" style="width: 100%;">
                </div>
              </section>
            ';
        } elseif ($pkgRec->maptype == 2 && !empty($pkgRec->mapgoogle)) {
            $respkg_detail .= '
              <section class="route-map-section mt-5">
                <div class="package_destination_map">
                  <h2 class="green-title section-title"><span>Package</span> Route Map</h2>
                  <iframe src="' . $pkgRec->mapgoogle . '" width="100%" height="450"></iframe>
                </div>
              </section>
            ';
        }
        
        $fixedDatesIdArr = [];
        $fixedDates = Packagedate::getPackage_limit($pkgRec->id);
        $fixedDatesIdArr = Packagedate::check_availability($fixedDates, $pkgRec->id);

        $respkg_detail .= '
          <div class="modal fade" id="fixedDepartureModal" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
              <div class="modal-content">

                <div class="modal-header ">
                  <h5 class=" green-title">
                    Reserve Your <span class="orange-text">Fixed Departure</span> Trip
                  </h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                ';
        if(!empty($fixedDatesIdArr)) {
            $firstFixedId = $fixedDatesIdArr[0];
            $firstFixed = Packagedate::find_by_id($firstFixedId);
            $start_date = date('d M Y', strtotime($firstFixed->package_date));
            $end_date_str = date('d M Y', strtotime($firstFixed->package_date . ' + ' . $pkgRec->days . ' days'));
            
            $date1 = new DateTime();
            $date2 = new DateTime($firstFixed->package_closure);
            $days = $date2->diff($date1)->format('%a');
            
            $respkg_detail .= '
                  <p class="book_before">Book before : '.date('d M Y', strtotime($firstFixed->package_closure)).'</p>
                  <ul class="fixed_dates">
                    <li class="trapezium-left">Start Date : '.$start_date.'</li>
                    <li class="square-middle">'.$days.' days Left</li>
                    <li class="trapezium-right">End Date : '.$end_date_str.'</li>
                  </ul>
            ';
        }
        
        $respkg_detail .= '
                  <form id="fixedDepartureForm">
                    <input type="hidden" name="pkg_id" value="' . $pkgRec->id . '">
                    <input type="hidden" name="fixed_date_id" value="' . (!empty($fixedDatesIdArr) ? $fixedDatesIdArr[0] : '') . '">
                    <input type="hidden" name="date" value="' . (!empty($firstFixed) ? $firstFixed->package_date : '') . '">
                    <input type="hidden" name="action" value="request_inquiry">

                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label for="peopleCount" class="form-label">Number of Travellers <span>*</span></label>
                        <input type="number" name="pax" class="peopleCount form-control" id="peopleCount" min="1" value="1"
                          required>
                      </div>

                      <div class="col-md-6 mb-3 d-flex align-items-end">
                        <div class="total-amount mx-auto">
                          <label class="form-label">Total Amount</label>
                          <div class="amount-box">$<span class="amountValue">'.($pkgRec->offer_price ?: $pkgRec->price).'</span></div>
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label class="form-label">Full Name <span>*</span></label>
                        <input type="text" name="full_name" class="form-control" required>
                      </div>

                      <div class="col-md-6 mb-3">
                        <label class="form-label">Address <span>*</span></label>
                        <input type="text" name="address1" class="form-control" required>
                      </div>
                    </div>
                    <div class="row">
                      <!-- Country -->
                      <div class="col-md-6 mb-3">
                        <label class="form-label" for="tripCountry">Country <span>*</span></label>
                        <select id="tripCountry" name="country" class="form-select tripCountry" required>
                          <option value="" disabled selected>Select Country</option>
                          <option value="Nepal" data-code="+977">Nepal</option>
                          <option value="India" data-code="+91">India</option>
                          <option value="United States" data-code="+1">United States</option>
                          <!-- add more countries -->
                        </select>
                      </div>

                      <!-- Code + Phone -->
                      <div class="col-md-6 mb-3">
                        <label class="form-label" for="tripPhone">Phone <span>*</span></label>
                        <div class="input-group">
                          <!-- Country Code (auto-filled) -->
                          <input type="text" name="country_code" class="input-group-text country-code-input" id="tripCode" value="+977" readonly style="width:80px;">
                          <!-- Phone Number -->
                          <input type="tel" name="phone" id="tripPhone" class="form-control" required aria-describedby="tripCode">
                        </div>
                      </div>
                    </div>

                    <div class="mb-3">
                      <label class="form-label">Email <span>*</span></label>
                      <input class="form-control" name="email" type="email" required>
                    </div>

                    <div class="mb-3">
                      <label class="form-label">Message</label>
                      <textarea class="form-control" name="message" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <div class="g-recaptcha" data-sitekey="6LdNE7osAAAAAArEtmA_zi-0FsIsmxHtYF_mH4ZZ"></div>
                    </div>

                    <div id="bookmsg"></div>

                    <button type="submit" class="btn btn-success w-50">
                      Submit
                    </button>

                  </form>
                </div>

              </div>
            </div>
          </div>
        ';
        
        $respkg_detail .= '
          <div class="modal fade" id="customizeModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
              <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                  <h5 class="modal-title green-title">
                    Customize <span class="orange-text">Your Trip</span>
                  </h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">

                  <form id="customizeForm" class="grid-form">
                    <input type="hidden" name="package" value="' . $pkgRec->id . '">
                    <input type="hidden" name="action" value="customize">

                    <!-- ROW 1 -->
                    <div class="two-col full">
                      <div class="form-group">
                        <label>Full Name <span>*</span></label>
                        <input type="text" name="name" required>
                      </div>

                      <div class="form-group">
                        <label>Address <span>*</span></label>
                        <input type="text" name="address" required>
                      </div>
                    </div>

                    <!-- ROW 2 -->
                    <div class="two-col full">
                      <!-- Country -->
                      <div class="form-group">
                        <label>Country <span>*</span></label>
                        <select id="customizeCountry" name="country" class="customizeCountry" required>
                          <option value="">Select a country</option>
                          <option value="Nepal" data-code="+977">Nepal</option>
                          <option value="India" data-code="+91">India</option>
                          <option value="United States" data-code="+1">United States</option>
                          <option value="Australia" data-code="+61">Australia</option>
                          <option value="United Kingdom" data-code="+44">United Kingdom</option>
                        </select>
                      </div>

                      <!-- Phone -->
                      <div class="form-group">
                        <label>Phone <span>*</span></label>
                        <div class="d-flex gap-2">
                          <input type="text" name="country_code" id="customizeCountryCode" class="country-code" placeholder="Code" readonly
                            style="max-width:120px">
                          <input type="tel" name="phone" id="customizePhone" placeholder="Phone Number" required>
                        </div>
                      </div>
                    </div>


                    <div class="form-group full">
                      <label>Email <span>*</span></label>
                      <input type="email" name="email" required>
                    </div>

                    <div class="form-group full">
                      <label>Message</label>
                      <textarea name="message" rows="3"></textarea>
                    </div>

                    <div class="form-group full">
                        <div class="g-recaptcha" data-sitekey="6LdNE7osAAAAAArEtmA_zi-0FsIsmxHtYF_mH4ZZ"></div>
                    </div>

                    <div id="customizeMsg"></div>

                    <div class="text-center mt-3">
                      <button type="submit" class="confirm-btn">Submit</button>
                    </div>

                  </form>

                </div>

              </div>
            </div>
          </div>
        ';
        
        $respkg_detail .= '
          <div class="modal fade" id="bookingModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
              <div class="modal-content">

                <!-- HEADER -->
                <div class="modal-header border-0">
                  <h2 class="green-title">
                    Your Adventure <span class="orange-text">Starts Here</span>
                  </h2>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- BODY -->
                <div class="modal-body">


                  <form id="bookingForm" class="grid-form mt-4">
                    <input type="hidden" name="pkg_id" value="' . $pkgRec->id . '">
                    <input type="hidden" name="action" value="request_inquiry">
                    <input type="hidden" name="price_per_person" id="pricePerPersonBooking" value="' . ($pkgRec->offer_price ?: $pkgRec->price) . '">
                    <input type="hidden" name="total_amount" id="totalAmountBooking" value="' . ($pkgRec->offer_price ?: $pkgRec->price) . '">

                    <!-- ROW 1 -->
                    <div class="form-group">
                      <label for="travelDate">Select Travel Date <span>*</span></label>
                      <input type="date" name="date" id="travelDate" required>
                    </div>

                    <div class="form-group">
                      <label for="peopleCountBooking">Number of Travellers <span>*</span></label>
                      <input type="number" name="pax" id="peopleCountBooking" min="1" value="1" required>
                    </div>

                    <div class="total-amount mx-auto">
                      <label>Total Amount</label>
                      <div class="amount-box">
                        $<span id="amountValueBooking">'.($pkgRec->offer_price ?: $pkgRec->price).'</span>
                      </div>
                    </div>

                    <!-- ROW 2 -->
                    <div class="two-col full">
                      <div class="form-group">
                        <label for="fullName">Full Name <span>*</span></label>
                        <input type="text" name="full_name" id="fullName" required>
                      </div>

                      <div class="form-group">
                        <label for="address">Address <span>*</span></label>
                        <input type="text" name="address1" id="address" required>
                      </div>
                    </div>

                    <!-- ROW 3 -->
                    <div class="two-col full form-row">
                      <!-- Country -->
                      <div class="form-group">
                        <label for="row3Country">Country <span>*</span></label>
                        <select name="country" id="row3Country" class="row3Country" required>
                          <option value="">Choose Your Country</option>
                          <option value="Nepal" data-code="+977">Nepal</option>
                          <option value="India" data-code="+91">India</option>
                          <option value="UAE" data-code="+971">UAE</option>
                          <option value="Bangladesh" data-code="+880">Bangladesh</option>
                          <option value="Sri Lanka" data-code="+94">Sri Lanka</option>
                        </select>
                      </div>

                      <!-- Phone -->
                      <div class="form-group">
                        <label class="d-block">Phone <span>*</span></label>
                        <div class="d-flex gap-2">
                          <input type="text" name="country_code" id="row3CountryCode" class="form-control country-code" placeholder="Code"
                            readonly style="max-width:120px;">
                          <input type="tel" name="phone" id="row3Phone" class="form-control" placeholder="Phone Number" required>
                        </div>
                      </div>
                    </div>

                    <!-- FULL WIDTH -->
                    <div class="form-group full">
                      <label for="email">Email <span>*</span></label>
                      <input type="email" name="email" id="email" required>
                    </div>

                    <div class="form-group full">
                      <label for="message">Message</label>
                      <textarea name="message" id="message" rows="3"></textarea>
                    </div>

                    <!-- RECAPTCHA -->
                    <div class="form-group full">
                        <div class="g-recaptcha" data-sitekey="6LdNE7osAAAAAArEtmA_zi-0FsIsmxHtYF_mH4ZZ"></div>
                    </div>


                    <div id="bookingMsg"></div>

                    <!-- BUTTON -->
                    <div class="full text-center">
                      <button type="submit" class="confirm-btn">Submit</button>
                    </div>

                  </form>

                </div>
              </div>
            </div>
          </div>
        ';
        
        $respkg_detail .= '
          <style>
            .features-column ul { list-style: none !important; padding: 0 !important; }
            .features-column li { position: relative !important; padding-left: 30px !important; margin-bottom: 10px !important; display: block !important; font-size: 16px; color: #333; }
            .features-column.include li::before { 
                content: "✓"; position: absolute; left: 0; top: 2px; width: 20px; height: 20px; 
                border-radius: 50%; border: 2px solid #2563eb; color: #2563eb; 
                display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: bold;
            }
            .features-column.exclude li::before { 
                content: "✕"; position: absolute; left: 0; top: 2px; width: 20px; height: 20px; 
                border-radius: 50%; border: 2px solid #ef4444; color: #ef4444; 
                display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: bold;
            }
          </style>
          <section class="whats-included-section">
            <section class="package-features">
              <h2 class="section-title green-title">
                <span>What\'s </span> Included?
              </h2>

              <div class="features-wrapper">

                <!-- INCLUDE -->
                <div class="features-column include">
                  <h3>Include</h3>
                  
                  ' . $pkgRec->incexc . '
                  <a href="#" class="view-more hide-on-empty">
                    <span class="icon plus">+</span> View More
                  </a>
                </div>

                <!-- EXCLUDE -->
                <div class="features-column exclude">
                  <h3>Exclude</h3>
                  ' . $pkgRec->booking_info . '
                  <a href="#" class="view-more hide-on-empty">
                    <span class="icon plus">+</span> View More
                  </a>
                </div>

              </div>

              <!-- POPUP OVERLAY -->
              <div class="popup-overlay" id="featuresPopup">
                <div class="popup-content">
                  <button class="popup-close" id="closePopup">&times;</button>
                  <div class="popup-sections">
                    <div class="popup-column">
                      <h3>Include</h3>
                      <div class="include_popup_check-list">
                      ' . $pkgRec->incexc . '
                      </div>
                    </div>
                    <div class="popup-column">
                      <h3>Exclude</h3>
                      <div class="exclude_popup_check-list">
                      ' . $pkgRec->booking_info . '
                      </div>
                    </div>
                  </div>
                </div>
              </div>

            </section>
          </section>
        ';

        $respkg_detail .= '
            </div>
            
            <aside class="package-right">

              <div class="price-card sticky">
                <!-- Price Card -->
                <div class="booking-wrapper">
                  <div class="price-card ">
                    <div class="price-container">
                      <h5>Starting</h5>
                      <div class="price">
        ';
        if(!empty($pkgRec->offer_price)){
            $respkg_detail .= '
                        <del>&#36;' . $pkgRec->price . '</del>
                        <span>&#36;' . $pkgRec->offer_price . '</span>
                         <small>/per person</small>
            ';
        } elseif(!empty($pkgRec->price)) {
            $respkg_detail .= '
                        <span>&#36;' . $pkgRec->price . '</span>
                         <small>/per person</small>
            ';
        } else {
            $respkg_detail .= '
                        <span>Contact Us</span>
            ';
        }
        $respkg_detail .= '
                       
                      </div>

                      <ul class="check-list">
                        <li>Your Safety is Our Top Priority</li>
                        <li>Travel with our experienced local guides</li>
                        <li>The cost will depend on the group size.</li>
                      </ul>
                      <button class="book-btn " data-bs-toggle="modal" data-bs-target="#bookingModal">
                        Book Now
                      </button>

                    </div><br>


                    <div class="price-container">
                      <section class="help-box mx-auto">
                        <header>
                          <button class="customize-btn" data-bs-toggle="modal" data-bs-target="#customizeModal">
                            Customize Your Trip
                          </button>

                        </header>
';

        $siteRegulars = Config::find_by_id(1);

        $emlAddress = str_replace('@', '&#64;', $siteRegulars->email_address);
        $emlAddress = str_replace('.', '&#46;', $emlAddress);
        $emails = explode("<br>", $emlAddress);
        $mail = '';
        $rawMail = '';
        if (!empty($emails)) {
            $mail = $emails[0];
            $rawMail = strip_tags(str_replace(['&#64;', '&#46;'], ['@', '.'], $emails[0]));
        }

        $contacts = explode("<br>", $siteRegulars->contact_info);
        $respkg_detail .= '
                        <address>
                          <ul class="contact-list">
';

        foreach ($contacts as $contact) {
            if (empty(trim($contact))) continue;
            
            $lowercaseContact = strtolower($contact);
            if (strpos($lowercaseContact, 'whatsapp') === false && strpos($lowercaseContact, 'whats app') === false) {
                continue;
            }

            $numbers = explode("(", $contact);
            $tel = trim($numbers[0]);
            
            $icon = '<i class="fa-brands fa-whatsapp whatsapp"></i>';
            
            $flag = '';
            if(strpos($tel, '+977') !== false) {
                $flag = '<img src="https://flagcdn.com/w40/np.png" alt="Nepal Flag">';
            } elseif(strpos($tel, '+49') !== false) {
                $flag = '<img src="https://flagcdn.com/w40/de.png" alt="Germany Flag">';
            } elseif(strpos($tel, '+975') !== false) {
                $flag = '<img src="https://flagcdn.com/w40/bt.png" alt="Bhutan Flag">';
            } elseif(strpos($tel, '+1') !== false) {
                $flag = '<img src="https://flagcdn.com/w40/us.png" alt="US Flag">';
            } elseif(strpos($tel, '+44') !== false) {
                $flag = '<img src="https://flagcdn.com/w40/gb.png" alt="UK Flag">';
            } elseif(strpos($tel, '+61') !== false) {
                $flag = '<img src="https://flagcdn.com/w40/au.png" alt="Australia Flag">';
            } else {
                $flag = '<i class="fa-solid fa-phone" style="margin-right: 8px;"></i>';
            }

            $cleanWaNum = preg_replace('/[^0-9]/', '', $tel);
            $respkg_detail .= '
                            <li>
                              '.$flag.'
                              <span><a href="https://wa.me/'.$cleanWaNum.'" target="_blank" style="color:inherit;text-decoration:none;">'.$tel.'</a></span>
                              <a href="https://wa.me/'.$cleanWaNum.'" target="_blank" style="color:inherit;text-decoration:none;">'.$icon.'</a>
                            </li>';
            
            break;
        }

        if (!empty($rawMail)) {
            $respkg_detail .= '
                            <li class="email">
                              <i class="fa-solid fa-envelope"></i>
                              <span data-bs-toggle="tooltip" data-bs-placement="right"
                                data-bs-title="'.$rawMail.'">
                                <a href="mailto:'.$rawMail.'">Email Us</a>
                              </span>
                            </li>';
        }

        $respkg_detail .= '
                          </ul>
                        </address>

                        <p class="tagline text-center">
                          "Your Trip, Your Way"
                        </p>
                      </section>

                    </div>
                  </div>
                </div>
              </div>
            </aside>
            </div>
            
        </section>
        
        <!-- Popular Packages similarly structured like in template -->
        <section class="popular-packages packages-wrapper deals-grid components">
          <h2 class="green-title text-center" id="packagesTitle">
            Similar <span class="orange-text">Packages</span>
          </h2>
          <br>
          <div class="package-grid-wrapper">
            <ul class="package-grid owl-carousel owl-theme" id="similarPackageCarousel">
        ';

        $similarTours = Package::get_filterpkg_by('', $pkgRec->activityId);
        foreach ($similarTours as $similarTour) {
            if ($similarTour->id != $pkgRec->id) {
                $file_path = SITE_ROOT . "images/package/" . $similarTour->image;
                $img = (!empty($similarTour->image) and file_exists($file_path)) ? IMAGE_PATH . "package/" . $similarTour->image : IMAGE_PATH . "static/home-featured.jpg";
                $destslug = Destination::field_by_id($similarTour->destinationId, 'slug');
                $dsRec = Destination::find_by_slug($destslug);
                $simAct = Activities::field_by_id($similarTour->activityId, 'title');
                
                $respkg_detail .= '
              <li class="package-card">
                <div class="image-wrapper">
                  <img src="' . $img . '" alt="' . $similarTour->title . '">
                  <span class="badge">' . ($similarTour->tags ?: "POPULAR") . '</span>
                </div>

                <div class="card-content">
                  <h3 class="title">' . $similarTour->title . '</h3>

                  <div class="card-info-wrapper">

                    <div class="info-row">
                      <div class="info">
                        <span class="label">Destination</span>
                        <span class="value green" title="' . $dsRec->title . '">' . $dsRec->title . '</span>
                      </div>
                      <div class="row-divider"></div>
                      <div class="info text-right">
                        <span class="label">Accomodations</span>
                        <span class="value green" title="Hotels">Hotels</span>
                      </div>
                    </div>

                    <div class="action-row">
                      <span class="days-badge">' . $similarTour->days . ' days</span>
                      <a href="' . BASE_URL . 'package/' . $similarTour->slug . '" class="explore_btn box_explore_btn">
                        <p>Explore</p>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                      </a>
                    </div>

                    <div class="info-row">
                      <div class="info">
                        <span class="label">Activities</span>
                        <span class="value green" title="' . $simAct . '">' . $simAct . '</span>
                      </div>
                      <div class="row-divider"></div>
                      <div class="info text-right">
                        <span class="label">Difficulty-level</span>
                        <span class="value green" title="' . $similarTour->difficulty . '">' . $similarTour->difficulty . '</span>
                      </div>
                    </div>

                  </div>
                </div>
              </li>
                ';
            }
        }

        $respkg_detail .= '
            </ul>
          </div>
        </section>
        </div>
        ';
        $respkg_detail .= '
        <script>
            // Package specific scripts
            document.addEventListener("DOMContentLoaded", () => {
                // Lightbox
                const galleryImages = Array.from(document.querySelectorAll("#hiddenGallery img")).map(img => img.src);
                const visibleImgs = document.querySelectorAll(".main-image img.gallery-img, .thumbnails img.gallery-img");

                if (galleryImages.length > 0) {
                    const lightbox = document.getElementById("lightbox");
                    const lightboxImg = document.getElementById("lightboxImg");
                    let index = 0;

                    visibleImgs.forEach((img, i) => {
                        img.addEventListener("click", () => {
                            index = i;
                            lightboxImg.src = galleryImages[index];
                            lightbox.style.display = "flex";
                        });
                    });

                    const viewAllBtn = document.querySelector(".view-more-btn");
                    if (viewAllBtn) {
                        viewAllBtn.addEventListener("click", () => {
                            index = 0;
                            lightboxImg.src = galleryImages[index];
                            lightbox.style.display = "flex";
                        });
                    }
                    
                    if (document.querySelector(".next")) {
                        document.querySelector(".next").onclick = () => {
                            index = (index + 1) % galleryImages.length;
                            lightboxImg.src = galleryImages[index];
                        };
                    }
                    
                    if (document.querySelector(".prev")) {
                        document.querySelector(".prev").onclick = () => {
                            index = (index - 1 + galleryImages.length) % galleryImages.length;
                            lightboxImg.src = galleryImages[index];
                        };
                    }
                    
                    if (document.querySelector(".close")) {
                        document.querySelector(".close").onclick = () => {
                            lightbox.style.display = "none";
                        };
                    }
                }
                
                // Features Popup
                const popup = document.getElementById("featuresPopup");
                const closeBtn = document.getElementById("closePopup");

                document.querySelectorAll(".view-more").forEach(btn => {
                    btn.addEventListener("click", e => {
                        e.preventDefault();
                        if (popup) {
                            popup.style.display = "flex";
                            document.body.style.overflow = "hidden";
                        }
                    });
                });

                if (closeBtn) {
                    closeBtn.addEventListener("click", () => {
                        popup.style.display = "none";
                        document.body.style.overflow = "";
                    });
                }

                if (popup) {
                    popup.addEventListener("click", e => {
                        if (e.target === popup) {
                            popup.style.display = "none";
                            document.body.style.overflow = "";
                        }
                    });
                }
                
                // Similar Packages Carousel
                if (typeof $ !== "undefined" && typeof $.fn.owlCarousel !== "undefined") {
                     $("#similarPackageCarousel").owlCarousel({
                        loop: true,
                        margin: 20,
                        items: 3,
                        autoplay: true,
                        autoWidth: false,
                        autoHeight: true,
                        autoplayTimeout: 3500,
                        autoplayHoverPause: true,
                        dots: true,
                        nav: false,
                        stagePadding: 0,
                        responsive: {
                            0: { items: 1 },
                            768: { items: 2 },
                            1200: { items: 3 }
                        }
                    });
                }
                
                // Booking calculation
                const peopleInput = document.getElementById("peopleCountBooking");
                const amountSpan = document.getElementById("amountValueBooking");
                const totalAmountInput = document.getElementById("totalAmountBooking");
                if (peopleInput && amountSpan) {
                    const pricePerPerson = parseFloat(amountSpan.textContent) || 0;
                    peopleInput.addEventListener("input", function() {
                        const count = parseInt(this.value) || 1;
                    const totalAmount = count * pricePerPerson;
                    amountSpan.textContent = totalAmount;
                    if (totalAmountInput) {
                      totalAmountInput.value = totalAmount;
                    }
                    });
                }
                
                // Travel date validation
                const today = new Date().toISOString().split("T")[0];
                document.querySelectorAll("input[type=\'date\']").forEach(input => {
                    input.setAttribute("min", today);
                });
            });
            
            // Tooltips
            document.addEventListener("DOMContentLoaded", function () {
              if (typeof bootstrap !== "undefined" && typeof bootstrap.Tooltip !== "undefined") {
                  const tooltipTriggerList = document.querySelectorAll("[data-bs-toggle=\'tooltip\']");
                  const tooltipList = [...tooltipTriggerList].map(el => new bootstrap.Tooltip(el));
              }
            });
        </script>
        ';
    } else {
        $url = BASE_URL . 'pages/errors';
        redirect_to($url);
    }
}


$jVars['module:package-breadcrumb'] = $respkg_breadcrumb;
$jVars['module:package-detail'] = $respkg_detail;
$jVars['module:package-review-modal'] = $send_review;
$jVars['module:package-email-friend'] = $email_friend;
$jVars['module:package-ask-question-modal'] = $ask_question;


?>