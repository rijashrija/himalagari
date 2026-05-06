<?php
/*
 * header module
 */

$header_components = '';
$header1 = '';
$home_header = '';

$header_components .= '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';

$destinationsHTML = '<option value="" disabled selected>Where to?</option>';
$destinationRec = Destination::get_destination();
if ($destinationRec) {
    foreach ($destinationRec as $dstRow) {
        $destinationsHTML .= '<option value="' . $dstRow->id . '">' . $dstRow->title . '</option>';
    }
}

$activitiesHTML = '<option value="" disabled selected>Select activity</option>';
$ActivitiesRec = Activities::get_activities();
if ($ActivitiesRec) {
    foreach ($ActivitiesRec as $actRow) {
        $activitiesHTML .= '<option value="' . $actRow->id . '">' . $actRow->title . '</option>';
    }
}

$durationHTML = '<option value="" disabled selected>Select duration</option>';
$durationRec = array('5' => '1-5 Days', '10' => '6-10 Days', '15' => '11-15 Days', 'morethan15' => 'More than 15 Days');
foreach ($durationRec as $k => $v) {
    $durationHTML .= '<option value="' . $k . '">' . $v . '</option>';
}

// Dynamic packages for inquiry form
$packagesHTML = '<option value="" disabled selected>Select a Package</option>';
$packageRec = Package::get_packages();
if ($packageRec) {
    foreach ($packageRec as $pkgRow) {
        $prc = !empty($pkgRow->offer_price) ? $pkgRow->offer_price : (!empty($pkgRow->price) ? $pkgRow->price : '');
        $packagesHTML .= '<option value="' . $pkgRow->id . '" data-price="' . htmlspecialchars($prc) . '">' . htmlspecialchars($pkgRow->title) . '</option>';
    }
}

// Country list with dial codes
$countriesList = [
    'AF' => ['name' => 'Afghanistan',          'code' => '+93'],
    'AL' => ['name' => 'Albania',               'code' => '+355'],
    'DZ' => ['name' => 'Algeria',               'code' => '+213'],
    'AR' => ['name' => 'Argentina',             'code' => '+54'],
    'AU' => ['name' => 'Australia',             'code' => '+61'],
    'AT' => ['name' => 'Austria',               'code' => '+43'],
    'BD' => ['name' => 'Bangladesh',            'code' => '+880'],
    'BE' => ['name' => 'Belgium',               'code' => '+32'],
    'BR' => ['name' => 'Brazil',                'code' => '+55'],
    'CA' => ['name' => 'Canada',                'code' => '+1'],
    'CN' => ['name' => 'China',                 'code' => '+86'],
    'CO' => ['name' => 'Colombia',              'code' => '+57'],
    'HR' => ['name' => 'Croatia',               'code' => '+385'],
    'CZ' => ['name' => 'Czech Republic',        'code' => '+420'],
    'DK' => ['name' => 'Denmark',               'code' => '+45'],
    'EG' => ['name' => 'Egypt',                 'code' => '+20'],
    'FI' => ['name' => 'Finland',               'code' => '+358'],
    'FR' => ['name' => 'France',                'code' => '+33'],
    'DE' => ['name' => 'Germany',               'code' => '+49'],
    'GR' => ['name' => 'Greece',                'code' => '+30'],
    'HK' => ['name' => 'Hong Kong',             'code' => '+852'],
    'HU' => ['name' => 'Hungary',               'code' => '+36'],
    'IN' => ['name' => 'India',                 'code' => '+91'],
    'ID' => ['name' => 'Indonesia',             'code' => '+62'],
    'IE' => ['name' => 'Ireland',               'code' => '+353'],
    'IL' => ['name' => 'Israel',                'code' => '+972'],
    'IT' => ['name' => 'Italy',                 'code' => '+39'],
    'JP' => ['name' => 'Japan',                 'code' => '+81'],
    'JO' => ['name' => 'Jordan',                'code' => '+962'],
    'KZ' => ['name' => 'Kazakhstan',            'code' => '+7'],
    'KE' => ['name' => 'Kenya',                 'code' => '+254'],
    'KR' => ['name' => 'South Korea',           'code' => '+82'],
    'KW' => ['name' => 'Kuwait',                'code' => '+965'],
    'MY' => ['name' => 'Malaysia',              'code' => '+60'],
    'MX' => ['name' => 'Mexico',                'code' => '+52'],
    'NL' => ['name' => 'Netherlands',           'code' => '+31'],
    'NZ' => ['name' => 'New Zealand',           'code' => '+64'],
    'NG' => ['name' => 'Nigeria',               'code' => '+234'],
    'NO' => ['name' => 'Norway',                'code' => '+47'],
    'NP' => ['name' => 'Nepal',                 'code' => '+977'],
    'OM' => ['name' => 'Oman',                  'code' => '+968'],
    'PK' => ['name' => 'Pakistan',              'code' => '+92'],
    'PH' => ['name' => 'Philippines',           'code' => '+63'],
    'PL' => ['name' => 'Poland',                'code' => '+48'],
    'PT' => ['name' => 'Portugal',              'code' => '+351'],
    'QA' => ['name' => 'Qatar',                 'code' => '+974'],
    'RO' => ['name' => 'Romania',               'code' => '+40'],
    'RU' => ['name' => 'Russia',                'code' => '+7'],
    'SA' => ['name' => 'Saudi Arabia',          'code' => '+966'],
    'SG' => ['name' => 'Singapore',             'code' => '+65'],
    'ZA' => ['name' => 'South Africa',          'code' => '+27'],
    'ES' => ['name' => 'Spain',                 'code' => '+34'],
    'LK' => ['name' => 'Sri Lanka',             'code' => '+94'],
    'SE' => ['name' => 'Sweden',                'code' => '+46'],
    'CH' => ['name' => 'Switzerland',           'code' => '+41'],
    'TH' => ['name' => 'Thailand',              'code' => '+66'],
    'TR' => ['name' => 'Turkey',                'code' => '+90'],
    'AE' => ['name' => 'UAE',                   'code' => '+971'],
    'GB' => ['name' => 'United Kingdom',        'code' => '+44'],
    'US' => ['name' => 'United States',         'code' => '+1'],
    'VN' => ['name' => 'Vietnam',               'code' => '+84'],
];

$countriesHTML = '<option value="" disabled selected>Select Country</option>';
foreach ($countriesList as $iso => $info) {
    $countriesHTML .= '<option value="' . $iso . '" data-code="' . $info['code'] . '">' . $info['name'] . '</option>';
}

$header_components .= '
      <div class="offcanvas offcanvas-end" id="menuCanvas">
        <div class="offcanvas-header">
          <h5 class="offcanvas-title">Menu</h5>
          <button
            class="btn-close"
            title="menu"
            data-bs-dismiss="offcanvas"
          ></button>
        </div>
        <div class="offcanvas-body sidebar">
          <jcms:module:offcanvas-menu/>
        </div>
      </div>


      <div class="bottom-search bottomSearch" id="bottomSearch">
        <div class="container">
          <!-- FORM -->
          <form class="row g-3 align-items-end" id="navSearchForm" action="' . BASE_URL . 'searchlist" method="post">
            <div class="col-md-3">
              <label class="form-label">Destination</label>
              <select id="destination" name="qdestination[]" class="form-control h-auto">
                ' . $destinationsHTML . '
              </select>
            </div>

            <div class="col-md-3">
              <label class="form-label">Activity</label>
              <select id="activity" name="qactivities[]" class="form-control h-auto">
                ' . $activitiesHTML . '
              </select>
            </div>

            <div class="col-md-3">
              <label class="form-label">Duration</label>
              <select id="duration" name="days" class="form-control h-auto">
                ' . $durationHTML . '
              </select>
            </div>

            <div class="col-md-3 search">
              <label class="form-label d-none d-md-block">&nbsp;</label>
              <button type="submit" class="explore_btn inquiry-btn w-100" id="inquiry-btn"><p>Search</p></button>
            </div>
          </form>
        </div>
      </div>

      <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvas_inquiry" aria-labelledby="offcanvas_inquiryLabel">
        <div class="inquiry_text">
          <div class="offcanvas-body">
            <div class="inquiry_title orange-text">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <span>Plan</span> Your Trip
              </div>
              <div class="offcanvas-header p-0">
                <button type="button" class="btn-close cross-icon" data-bs-dismiss="offcanvas" aria-label="Close"></button>
              </div>
            </div>
              <div id="planTripMsg"></div>
              <form id="planTripForm">
                <!-- Packages & Date -->
                <div class="form-row">
                  <div class="form-group">
                    <label>Package <span>*</span></label>
                    <select id="inq_package" name="package" required>
                      ' . $packagesHTML . '
                    </select>
                  </div>

                  <div class="form-group">
                    <label>Travel Date <span>*</span></label>
                    <input type="date" name="trip_date" id="inq_date" class="travel-date" required />
                  </div>
                </div>

                <!-- Pax & Price -->
                <div class="form-row">
                  <div class="form-group">
                    <label>No. of Pax <span class="required">*</span></label>
                    <input type="number" id="inq_pax" name="pax" min="1" value="1" required />
                  </div>

                  <div class="form-group">
                    <label>Price <span class="required">*</span></label>
                    <input type="text" id="inq_price" name="price" readonly required />
                  </div>
                </div>

                <!-- Name & Email -->
                <div class="form-row">
                  <div class="form-group">
                    <label>Full Name <span class="required">*</span></label>
                    <input type="text" name="name" id="inq_name" required />
                  </div>

                  <div class="form-group">
                    <label>Email Address <span class="required">*</span></label>
                    <input type="email" name="email" id="inq_email" required />
                  </div>
                </div>

                <!-- Country, Code & Phone -->
                <div class="form-row">
                  <div class="form-group">
                    <label>Country <span class="required">*</span></label>
                    <select id="planCountry" name="country" required>
                      ' . $countriesHTML . '
                    </select>
                  </div>

                  <div class="form-group" style="max-width: 90px;">
                    <label>Code</label>
                    <input
                      type="text"
                      id="planCountryCode"
                      name="country_code"
                      readonly
                    />
                  </div>

                  <div class="form-group phone-box">
                    <label>Contact No. <span class="required">*</span></label>
                    <input type="tel" name="phone" id="inq_phone" required />
                  </div>
                </div>

                  <div class="form-group mb-3">
                    <div class="g-recaptcha" data-sitekey="6LcXLDMsAAAAAOcRdgFpghRi7swX639Y1zIo6EJ3"></div>
                  </div>

                  <div class="form-row justify-content-center">
                    <button type="submit" class="explore_btn inquiry-btn"><p>Submit Inquiry</p></button>
                  </div>
                </form>
            </div>
          </div>
        </div>
      </div>
      ';


$header1 = '
<style>
/* Global fix to ensure navbar links hide on mobile devices across all pages */
@media (max-width: 991px) {
  .navbar-nav { display: none !important; }
}
/* Urgent fix for burger icon cutoff on mobile devices (iPhone Pro Max) */
@media (max-width: 480px) {
  .navbar, .original_nav { padding-left: 8px !important; padding-right: 12px !important; }
  .navbar .container-fluid, .original_nav .container-fluid { padding: 0 !important; }
  .navbar-brand img { width: 45px !important; }
  .navbar .inquiry-btn, .original_nav .inquiry-btn { margin: 0 2px !important; padding: 5px 8px !important; font-size: 11px !important; height: 30px !important; }
  .navbar .d-flex, .original_nav .d-flex { gap: 2px !important; }
  .navbar .icon-btn, .original_nav .icon-btn { margin-left: 0 !important; width: 28px !important; height: 28px !important; font-size: 14px !important; }
  .navbar .navbar-toggler, .original_nav .navbar-toggler { padding: 2px !important; margin-left: 0 !important; width: auto !important; }
  .navbar-toggler-icon { width: 1.1em !important; height: 1.1em !important; }
}
/* Urgent fix for burger icon cutoff on mobile devices (iPhone Pro Max) */
@media (max-width: 480px) {
  .navbar, .original_nav { padding-left: 8px !important; padding-right: 12px !important; }
  .navbar .container-fluid, .original_nav .container-fluid { padding: 0 !important; }
  .navbar-brand img { width: 45px !important; }
  .navbar .inquiry-btn, .original_nav .inquiry-btn { margin: 0 2px !important; padding: 5px 8px !important; font-size: 11px !important; height: 30px !important; }
  .navbar .d-flex, .original_nav .d-flex { gap: 2px !important; }
  .navbar .icon-btn, .original_nav .icon-btn { margin-left: 0 !important; width: 28px !important; height: 28px !important; font-size: 14px !important; }
  .navbar .navbar-toggler, .original_nav .navbar-toggler { padding: 2px !important; margin-left: 0 !important; width: auto !important; }
  .navbar-toggler-icon { width: 1.1em !important; height: 1.1em !important; }
}
</style>
<div class="header-container">
      <nav class="navbar navbar-dark">
        <div class="container-fluid">
          <!-- LOGO (ALWAYS) -->
          ' . $jVars['site:logo'] . '

          <!-- NAV LINKS -->

        <jcms:module:menu/>

          <!-- RIGHT ICONS (ALWAYS) -->
          <div class="d-flex align-items-center gap-3">
            <!-- SEARCH ICON -->
            <button
              title="search"
              class="icon-btn searchToggle"
              id="searchToggle"
            >
              <i class="fa-solid fa-magnifying-glass"></i>
            </button>

         <button class="explore_btn inquiry-btn actual_navbar normal_button" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_inquiry">
              <p>Plan Your Trip</p>
            </button>

            <button
              title="menu"
              class="navbar-toggler"
              type="button"
              data-bs-toggle="offcanvas"
              data-bs-target="#menuCanvas"
            >
              <span class="navbar-toggler-icon"></span>
            </button>
          </div>
        </div>
      </nav>

      
  '. $header_components .'
';


  $home_header = '
<style>
/* Global fix to ensure navbar links hide on mobile devices across all pages */
@media (max-width: 991px) {
  .navbar-nav { display: none !important; }
}
/* Urgent fix for burger icon cutoff on mobile devices (iPhone Pro Max) */
@media (max-width: 480px) {
  .navbar, .original_nav { padding-left: 8px !important; padding-right: 12px !important; }
  .navbar .container-fluid, .original_nav .container-fluid { padding: 0 !important; }
  .navbar-brand img { width: 45px !important; }
  .navbar .inquiry-btn, .original_nav .inquiry-btn { margin: 0 2px !important; padding: 5px 8px !important; font-size: 11px !important; height: 30px !important; }
  .navbar .d-flex, .original_nav .d-flex { gap: 2px !important; }
  .navbar .icon-btn, .original_nav .icon-btn { margin-left: 0 !important; width: 28px !important; height: 28px !important; font-size: 14px !important; }
  .navbar .navbar-toggler, .original_nav .navbar-toggler { padding: 2px !important; margin-left: 0 !important; width: auto !important; }
  .navbar-toggler-icon { width: 1.1em !important; height: 1.1em !important; }
}
</style>
        <nav class="navbar navbar-dark original_nav">
        <div class="container-fluid">
          <!-- LOGO (ALWAYS) -->
           ' . $jVars['site:logo'] . '

          <!-- NAV LINKS -->
          <jcms:module:menu_home/>

          <!-- RIGHT ICONS (ALWAYS) -->
          <div class="d-flex align-items-center">
            <!-- SEARCH ICON -->
            <button title="search" class="icon-btn searchToggle">
              <i class="fa-solid fa-magnifying-glass"></i>
            </button>

            <button class="explore_btn inquiry-btn actual_navbar normal_button" data-bs-toggle="offcanvas"
              data-bs-target="#offcanvas_inquiry">
              <p>Plan Your Trip</p>
            </button>

            <button title="menu" class="navbar-toggler" type="button" data-bs-toggle="offcanvas"
              data-bs-target="#menuCanvas">
              <span class="navbar-toggler-icon"></span>
            </button>
          </div>
        </div>
      </nav>

  
  
  ';




$jVars['module:header'] = $header1;
$jVars['module:home_header'] = $home_header;
$jVars['module:header_components'] = $header_components;

?>