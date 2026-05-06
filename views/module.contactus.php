<?php
/* 
 * Module Contact us
 */
$rescntbdc = $rescontct = $rescontct1 = '';

if (defined('CONTACTUS_PAGE')) {

  $rescntbdc .= '<div class="opacy_bg_02">
     <div class="container">
        <h3>Contact Us</h3>
        <div class="crumbs">
            <ul>
                <li><a href="' . BASE_URL . 'home">Home</a></li>
                <li>/</li>
                <li>Contact Us</li>                                         
            </ul>    
        </div>
    </div>  
</div>  
<!-- End Content Parallax-->';
  /* End Breadcrumb block */

  $rescontct .= '
        <section>
        <div class="form form-spac rows">
            <div class="container">
                <!-- TITLE & DESCRIPTION -->
                <!--<div class="spe-title col-md-12">
                    <h2>CHO-OYU TREKKING<span> Contact us</span></h2>
                    <div class="title-line">
                        <div class="tl-1"></div>
                        <div class="tl-2"></div>
                        <div class="tl-3"></div>
                    </div>
                    <p>EXPLORE YOUR DREAMS</p>
                </div>-->
                <div class="col-md-6 col-sm-6 col-xs-12 form_1 wow fadeInLeft" data-wow-duration="1s">
                   
                    <form id="ajaxform" name="home_form" action="" method="post">
                        <ul>
                            <li>
                                <label class="text-black" for="fname">Name</label>
                                <input type="text" name="ename" value="" id="ename">
                            </li>
                            <li>
                                <label class="text-black" for="eemail">Email</label>
                                <input type="email" name="eemail" value="" id="eemail">
                            </li>
                            <li>
                                <label class="text-black" for="emobile">Contact</label>
                                <input type="tel" name="emobile" value="" id="emobile">
                            </li>
                            <li>
                                <label class="text-black" for="emobile">Your Comments</label>
                                <textarea name="emess" cols="40" rows="3" id="text-comment" placeholder="Enter your message"></textarea>
                            </li>
                            <div class="form-group col-md-3">
                        <label for="inputCode4">Human Verification</label>
                        <img src=' . BASE_URL . '/captcha/imagebuilder.php?rand=310333 border="1" class="text-field" onclick="updateCaptcha(this);">
                    </div>
                    <div class="form-group col-md-9 pt-4 mt-2">
                        <input type="text" class="form-control" id="inputCode4"
                               placeholder="Type the verification code" name="userstring" maxlength="5">
                    </div>
                            <li>
                                <input type="submit" value="Submit" id="send_button">
                            </li>        
                        </ul>
                    </form>
                </div>
                <!--====== COMMON NOTICE ==========-->
                <div class="col-md-6 col-sm-6 col-xs-12 wow fadeInRight" data-wow-duration="1s">
                    <div class="rows book_poly">' . $jVars['module:locationinfo'] . '</div>
                </div>
            </div>
        </div>
    </section>
               ';


  $locationInfo = $jVars['module:locationinfo'];
  $baseUrl      = BASE_URL;

  $rescontct1 = <<<HTML

    <section class="contact-section">
        <h2 class="contact-title green-title text-center">
          Let's Start Planning Your
          <span class="orange-text">Journey</span> Together
        </h2>
        <section class="contact-wrapper">
          <!-- LEFT -->
          {$locationInfo}

          <!-- FORM -->
          <div class="contact-form">
            <h2 class="orange-text text-start">Get In Touch</h2>
            <h3 class="mb-4">We're here to help with your travel plans.</h3>
            <div id="contactMsg"></div>
            <form id="contactForm">
              <label class="form-label">
                Full Name <span class="required">*</span>
              </label>
              <input type="text" name="name" placeholder="Full Name" required />
              <label class="form-label">Address</label>
              <input type="text" name="address" placeholder="Address" />
              <label class="form-label">
                Email Address <span class="required">*</span>
              </label>
              <input type="email" name="email" placeholder="Email Address" required />

              <div class="form-row">
                <select name="country" title="country" id="countrySelect" required
                  onchange="document.getElementById('countryCode').value = this.options[this.selectedIndex].getAttribute('data-code') || '';">
                  <option value="" data-code="">Choose Your Country</option>
                  <option value="Nepal" data-code="+977">Nepal</option>
                  <option value="India" data-code="+91">India</option>
                  <option value="UAE" data-code="+971">UAE</option>
                  <option value="Bangladesh" data-code="+880">Bangladesh</option>
                  <option value="Sri Lanka" data-code="+94">Sri Lanka</option>
                </select>
                <p class="phone-field">
                  <input
                    type="text"
                    name="country_code"
                    title="country-code"
                    id="countryCode"
                    class="country-code"
                    placeholder="Code"
                    readonly
                  />
                  <input type="text" name="emobile" placeholder="Phone Number" required />
                </p>
              </div>

              <label class="form-label">
                Questions &amp; Comments <span class="required">*</span>
              </label>
              <textarea
                name="message"
                placeholder="Any Suggestions, Inquiry, Feedbacks?"
                required
              ></textarea>
              <div class="form-group mb-3">
                <div class="g-recaptcha" data-sitekey="6LcXLDMsAAAAAOcRdgFpghRi7swX639Y1zIo6EJ3"></div>
              </div>
              <button type="submit" class="explore_btn inquiry-btn w-50 mx-auto">
                Submit
              </button>
            </form>
          </div>

        </section>

        <!-- LOCATIONS -->
        <section class="branches">
          {$jVars['module:locationbreif']}
        </section>
      </section>

HTML;
}

$jVars['module:contactus-breadcrumb'] = $rescntbdc;
$jVars['module:contact-form'] = $rescontct1;

?>