<?php
/*
* Package Date Module
*/

$resfixed = '';
if (defined('HOME_PAGE')) {
    // Fetch upcoming fixed departures
    // We join with package to get details
    $sql = "SELECT pd.*, p.title as pkg_title, p.slug as pkg_slug, p.image as pkg_image, p.days as pkg_days, p.popular as pkg_popular, p.price as pkg_price, p.offer_price as pkg_offer_price
            FROM tbl_package_date pd
            JOIN tbl_package p ON pd.package_id = p.id
            WHERE pd.status = '1' AND pd.package_date >= CURDATE()
            ORDER BY pd.package_date ASC
            LIMIT 24";
    $fixedRec = Packagedate::find_by_sql($sql);

    if ($fixedRec) {
        $resfixed .= '
        <section class="packages-wrapper components text-center" id="section3">
            <header class="page-header text-center">
                <h4 class="text-center">Fixed Departure</h4>
                <h2 class="mb-3 green-title text-center">
                    Hassle-Free
                    <span class="orange-text">Fixed Departure</span> Adventures.
                </h2>
            </header>
            <br />

            <div class="packages-grid package--lower mx-auto">';

        $fdCount = 0;
        foreach ($fixedRec as $row) {
            $fdCount++;
            $file_path = SITE_ROOT . "images/package/" . $row->pkg_image;
            $img = (!empty($row->pkg_image) and file_exists($file_path)) ? IMAGE_PATH . "package/" . $row->pkg_image : IMAGE_PATH . "static/home-featured.jpg";
            
            $popularBadge = ($row->pkg_popular == 1) ? '<span class="badge">POPULAR</span>' : '';
            
            // Format dates
            $startDate = date('d M Y', strtotime($row->package_date));
            $endDate = date('d M Y', strtotime($row->package_date . ' + ' . $row->pkg_days . ' days'));
            $bookBefore = date('d M Y', strtotime($row->package_closure));

            // Determine price
            $actualPrice = !empty($row->package_rate) ? $row->package_rate : (!empty($row->pkg_offer_price) ? $row->pkg_offer_price : $row->pkg_price);
            $hideStyle = ($fdCount > 6) ? ' style="display:none;"' : '';

            $resfixed .= '
            <div class="package-card">
                <div class="image-wrapper">
                    <a href="' . BASE_URL . 'package/' . $row->pkg_slug . '">
                        <img src="' . $img . '" alt="' . htmlspecialchars($row->pkg_title, ENT_QUOTES, 'UTF-8') . '" />
                    </a>
                    ' . $popularBadge . '
                </div>

                <div class="card-content">
                    <a href="' . BASE_URL . 'package/' . $row->pkg_slug . '">
                        <h3 class="title">' . htmlspecialchars($row->pkg_title, ENT_QUOTES, 'UTF-8') . '</h3>
                    </a>

                    <div class="card-info-wrapper">

                      <div class="info-row">
                        <div class="info">
                          <span class="label">Trip Start Date</span>
                          <span class="value green">' . $startDate . '</span>
                        </div>
                        <div class="row-divider"></div>
                        <div class="info text-right">
                          <span class="label">Seats Available</span>
                          <span class="value green">' . $row->package_seats . '</span>
                        </div>
                      </div>

                      <div class="action-row">
                        <span class="days-badge">' . $row->pkg_days . ' days</span>
                        <button class="explore_btn" data-bs-toggle="modal" data-bs-target="#fixedDepartureModal" 
                                data-seats="' . $row->package_seats . '" 
                                data-pkgid="' . $row->package_id . '" 
                                data-dateid="' . $row->id . '"
                                data-price="' . $actualPrice . '"
                                data-title="' . htmlspecialchars($row->pkg_title, ENT_QUOTES, 'UTF-8') . '"
                                data-start="' . $startDate . '"
                                data-end="' . $endDate . '"
                                data-before="' . $bookBefore . '">
                          <p>Book Now</p>
                          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                          </svg>
                        </button>
                      </div>

                      <div class="info-row">
                        <div class="info">
                          <span class="label">Book Before</span>
                          <span class="value green">' . $bookBefore . '</span>
                        </div>
                        <div class="row-divider"></div>
                        <div class="info text-right">
                          <span class="label">Trip End Date</span>
                          <span class="value green">' . $endDate . '</span>
                        </div>
                      </div>

                    </div>
                </div>
            </div>';
        }

        $resfixed .= '
            </div>';
        
        if (count($fixedRec) > 6) {
            $resfixed .= '
  <div class="more-btn load-more-wrap centred"><a href="javascript:void(0)" id="toggle-btn" class="explore_btn inquiry-btn"><p>Load More</p></a></div>
            ';
        }

        $resfixed .= '
        </section>';
    }
}

$jVars['module:fixed-departure-home'] = $resfixed;
?>
