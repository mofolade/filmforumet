<?php
  session_start();
?>
<!DOCTYPE html>
<html lang="en">
    <?php
        $currentUserRoles=[];
        $page_title = "Filmforumet - Topic";
        include_once 'views/head.php';  
    ?>
    <body>
        <div id="app">
            <main>
                <?php include 'views/header.php';?>
                <div class="wrapper">
                    <div class="topic-container">
                        <div id="about-wrapper">
                            <div class="about-section">
                                <h2>Filmforumet</h2>
                                <p>We're listening to you.</p>
                            </div>

                            <div class="about-row">
                            <div class="about-column">
                                <div class="about-card">
                                <div class="about-container">
                                    <h2>Eva Schmidt</h2>
                                    <p class="about-title">CEO & Founder</p>
                                    <p>Lorem ipsum ipsum lorem.</p>
                                    <p>eva@auctionista.com</p>
                                </div>
                                </div>
                            </div>

                            <div class="about-column">
                                <div class="about-card">
                                <div class="about-container">
                                    <h2>Ralf Holm</h2>
                                    <p class="about-title">Ekonomi chef</p>
                                    <p>Lorem ipsum ipsum lorem.</p>
                                    <p>ralf@auctionista.com</p>
                                </div>
                                </div>
                            </div>
                            
                            <div class="about-column">
                                <div class="about-card">
                                <div class="about-container">
                                    <h2>Magnus Cederström</h2>
                                    <p class="about-title">Designer</p>
                                    <p>Lorem ipsum ipsum lorem.</p>
                                    <p>magnus@auctionista.com</p>
                                </div>
                                </div>
                            </div>
                            </div>

                            <div class="about-section">
                            <h2><span><p>Contact information</p></span></h2>
                            <div class="clearboth"></div>

                            <div class="about-row">

                                <div class="about-column">  
                                <div class="mk-box-icon-2-icon size-48">        
                                    <svg class="mk-svg-icon" data-name="mk-moon-location-3" data-cacheid="icon-5fbb559b80160" style=" height:48px; width: 48px; fill: white;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                    <path d="M256 480c-88.366 0-160-71.634-160-160 0-160 160-352 160-352s160 192 160 352c0 88.366-71.635 160-160 160zm0-258c-54.124 0-98 43.876-98 98s43.876 98 98 98 98-43.876 98-98-43.876-98-98-98zm-62 98a62 62 1260 1 0 124 0 62 62 1260 1 0-124 0z" transform="scale(1 -1) translate(0 -480)"></path></svg>     
                                </div>    
                                <h3 class="mk-box-icon-2-title">Address</h3>    
                                <p class="mk-box-icon-2-content">
                                    <span style="color: #ffffff;">Storgatan 80.<br>227 61 Lund</span>
                                </p>
                                </div>
                                
                                <div class="about-column">  
                                <div class="mk-box-icon-2-icon size-48">        
                                    <svg class="mk-svg-icon" data-name="mk-moon-phone-4" data-cacheid="icon-5fbb559b806b4" style=" height:48px; width: 48px; fill: white;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                    <path d="M321.788 371.146c-11.188 6.236-20.175 2.064-32.764-4.572-11.46-8.748-45.402-35.438-81.226-71.188-26.156-33.084-46.162-64.288-55.375-79.293-.625-1.66-.944-2.632-.944-2.632-5.397-13.476-8.771-22.92-1.324-33.521 6.854-9.727 9.5-12.383 18.24-20.108l-87.79-130.124c-10.604 7.728-27.018 25.106-40.509 44.378-12.538 18.317-23.154 38.587-26.049 53.055 15.295 55.117 52.258 157.896 120.583 231.325l-.021.308c65.73 81.028 170.165 131.43 225.571 153.226 14.679-1.385 35.938-9.844 55.456-20.404 20.598-11.415 39.567-25.945 48.329-35.685l-120.288-100.829c-8.597 7.91-11.498 10.254-21.889 16.064zm-116.178-242.488c7.241-5.302 5.313-14.944 1.926-20.245l-66.579-101.913c-4.344-5.291-13.396-8.064-21.252-5.579l-27.433 18.381 88.034 129.879 25.304-20.523zm287.339 269.188l-94.473-76.788c-4.93-3.918-14.313-6.838-20.325-.188l-23.046 23.05 120.047 101.015 21.136-25.357c3.285-7.564 1.467-16.857-3.339-21.732z"></path></svg>     
                                </div>    
                                <h3 class="mk-box-icon-2-title">Call us</h3>    
                                <p class="mk-box-icon-2-content">
                                    <span style="color: #ffffff;"><b>Customer service</b><br><br>+46 555 555 555</span>
                                </p>
                                </div>
                                
                                <div class="about-column"> 
                                <div class="mk-box-icon-2-icon size-48">        
                                    <svg class="mk-svg-icon" data-name="mk-moon-bubble-dots" data-cacheid="icon-5fbb559b80b7e" style=" height:48px; width: 48px; fill: white;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                    <path d="M418 32h-324c-51.7 0-94 42.3-94 94v354l96-96h322c51.7 0 94-42.3 94-94v-164c0-51.7-42.3-94-94-94zm-258 224c-17.673 0-32-14.327-32-32s14.327-32 32-32 32 14.327 32 32-14.327 32-32 32zm96 0c-17.673 0-32-14.327-32-32s14.327-32 32-32 32 14.327 32 32-14.327 32-32 32zm96 0c-17.673 0-32-14.327-32-32s14.327-32 32-32 32 14.327 32 32-14.327 32-32 32z"></path></svg>     
                                </div>    
                                <h3 class="mk-box-icon-2-title">Email</h3>    
                                <p class="mk-box-icon-2-content"><span style="color: #ffffff;"><b>Customer service e-mail</b></span><br><br>
                                    <span style="color: #ffffff;"> <a style="color: #ffffff;" href="mailto:info@auctionist.se">info@filmforum.se</a></span>
                                </p>
                                </div>
                            </div>
                            </div>
                            <div class="clearboth"></div>
                        </div>                
                    </div>
                </div>
            </main>
        </div>
    </body>
</html>
