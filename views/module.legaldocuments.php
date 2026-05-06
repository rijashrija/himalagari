<?php
$res_legal = '';

// Find the ID of the gallery named "Legal Documents"
$sql_gallery = "SELECT id FROM tbl_galleries WHERE title='Legal Documents' AND status=1 LIMIT 1";
$galleryRec = Gallery::find_by_sql($sql_gallery);

if ($galleryRec) {
    $galleryId = $galleryRec[0]->id;
    // Fetch all images for this gallery
    $documentImages = GalleryImage::getImagelist_by($galleryId);

    if ($documentImages) {
        $res_legal .= '
        <h2 class="green-title">Legal <span class="orange-text">Documents</span></h2>
        <section class="team-section legal-section container mx-auto">
          <!-- legal docs -->
          <div class="legal">
            <ul class="legal-grid">';

        foreach ($documentImages as $row) {
            $file_path = SITE_ROOT . 'images/gallery/galleryimages/' . $row->image;
            if (file_exists($file_path) && !empty($row->image)) {
                $imgSrc = IMAGE_PATH . 'gallery/galleryimages/' . $row->image;
                $res_legal .= '
                <li>
                  <div class="docs_wrapper">
                    <img src="' . $imgSrc . '" class="popup-img" alt="' . $row->title . '">
                    <div class="caption_overlay">' . $row->title . '</div>
                    <span class="search-icon">
                      <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                  </div>
                </li>';
            }
        }

        $res_legal .= '
            </ul>
          </div>
          <!-- Lightbox Popup Structure (shared by the images) -->
          <div id="imgPopup" class="img-popup">
            <span class="close">&times;</span>
            <button class="nav-btn prev">&#10094;</button>
            <img class="popup-content" id="popupImage" alt="">
            <button class="nav-btn next">&#10095;</button>
          </div>
        </section>';
    }
}

$jVars['module:legaldocuments'] = $res_legal;
?>
