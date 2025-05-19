<?php

$title = $data['name'];
$phone = $data['phone'];
$email = $data['email'];
$website_url = $data['website_url'];
$address = $data['address'];
$logo = $data['logo'];
$image = $data['image'];
$short_description = $data['short_description'];
$description = $data['description'];
$tags = $data['tags'];
$categories = $data['categories'];
$latitude = $data['latitude'];
$longitude = $data['longitude'];
$meta = $data['meta'];
$socialLinks = $data['social_links'];


$fake_image = EHX_DIRECTORIST_PLUGIN_URL . 'assets/images/cover.jpg';
$fake_logo = EHX_DIRECTORIST_PLUGIN_URL . 'assets/images/logo.jpg';

?>

<div class="ehxd-container">
    <div class="ehxd-header">
        <div class="ehxd-cover-img">
            <?php
            $image_url = !empty($image) ? $image : $fake_image;
            ?>
            <img src="<?php echo esc_url($image_url); ?>" alt="Feature image">
        </div>
        <div class="ehxd-logo">
            <?php
            $logo_url = !empty($logo) ? $logo : $fake_logo;
            ?>
            <img src="<?php echo esc_url($logo_url); ?>" alt="logo image">
        </div>
    </div>

    <div class="ehxd-main">
        <div class="ehxd-left-section">
            <div class="ehxd_title">
                <h1><?php echo esc_html($title); ?></h1>
            </div>

            <div class="ehxd-brief">
                <h2 class="ehxd-section-title">Brief Description</h2>
                <p class="ehxd-brief-description">
                    <?php echo esc_html($short_description); ?>
                </p>
            </div>
            <?php if (!empty($description)): ?>
                <div class="ehxd-description">
                    <h2 class="ehxd-section-title">Description</h2>
                    <div class="ehxd-full-description">
                        <p>
                            <?php echo esc_html($description); ?>
                        </p>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (!empty($meta)): ?>
                <div class="ehxd-contact-help">
                    <?php foreach ($meta as $data): ?>
                        <?php if (!empty($data['label']) && !empty($data['value'])): ?>
                            <h2 class="ehxd-section-title"><?php echo esc_html($data['label']); ?></h2>

                            <p class="ehxd_section_describe">
                                <?php echo esc_html($data['value']); ?>
                            </p>
                        <?php endif; ?>
                    <?php endforeach; ?>

                </div>
            <?php endif; ?>

            <div class="ehxd-category-and-tag-wrapper">
                <div class="ehxd_category_wrapper ehxd_border">
                    <h2 class="ehxd-section-title">Category name</h2>
                    <div class="ehxd-category-tags">
                        <?php foreach ($categories as $category): ?>
                            <span class="ehxd-category"><?php echo esc_html($category['name']); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="ehxd_category_wrapper">
                    <h2 class="ehxd-section-title">Tag name</h2>
                    <div class="ehxd-category-tags">
                        <?php foreach ($tags as $tag): ?>
                            <span class="ehxd-tag"><?php echo esc_html($tag['name']); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>
        </div>

        <div class="ehxd-right-section">
            <div class="ehxd-details-wrapper">
                <div class="ehxd-map-placeholder">
                    <div id="ehxd-map" class="ehxd-map-marker"> </div>
                </div>
                <div class="ehxd-contact-info">
                    <div class="ehxd-info-item">
                        <div class="ehxd-info-icon"><i class="fa-solid fa-location-dot"></i></div>
                        <p class="ehxd-info"><?php echo esc_html($address); ?></p>
                    </div>
                    <div class="ehxd-info-item">
                        <div class="ehxd-info-icon"><i class="fa-solid fa-at"></i></span></div>
                        <p class="ehxd-info"><?php echo esc_html($email); ?></p>
                    </div>
                    <div class="ehxd-info-item">
                        <div class="ehxd-info-icon"><i class="fa-solid fa-phone"></i></div>
                        <p class="ehxd-info"><?php echo esc_html($phone); ?></p>
                    </div>
                    <?php if (!empty($website_url)) : ?>
                        <div class="ehxd-info-item">
                            <div class="ehxd-info-icon"><i class="fa-solid fa-globe"></i></div>
                            <p class="ehxd-info-website ehxd-info">
                                <a href="<?php echo esc_url($website_url); ?>" target="_blank" rel="noopener">
                                    <?php echo esc_html($website_url); ?>
                                </a>
                            </p>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($socialLinks) && is_array($socialLinks)) : ?>
                        <div class="ehxd-social-links">
                            <?php foreach ($socialLinks as $social) :
                                $icon = isset($social['icon']) ? esc_attr($social['icon']) : '';
                                $url = !empty($social['url']) ? esc_url($social['url']) : '#';
                            ?>
                                <div class="ehxd_social_icon">
                                    <a href="<?php echo $url; ?>" target="_blank" rel="noopener" class="ehxd-social-icon">
                                        <i class="fab fa-<?php echo $icon; ?>"></i>
                                    </a>
                                </div>

                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="ehxd-owner-section">
                <div class="ehxd-owner-info">
                    <div class="ehxd-img-wrapper">
                        <img src="<?php echo $logo; ?>" alt="admin">
                    </div>
                    <div>
                        <div class="ehxd-owner-name"><?php echo esc_html($title); ?></div>
                        <div class="ehxd-owner-title"><?php echo esc_html($email); ?></div>
                    </div>
                </div>

                <div class="ehxd-contact-form">
                    <form id="ehxd-contact-form">
                        <div class="ehxd-form-group">
                            <input type="hidden" name="listing_email" value="<?php echo esc_html($email); ?>">
                            <label class="ehxd-form-label">Name <span style="color: #e96a6a;">*</span></label>
                            <input name="name" type="text" class="ehxd-form-input" placeholder="Your name" require>
                        </div>
                        <div class="ehxd-form-group">
                            <label class="ehxd-form-label">Email <span style="color: #e96a6a;">*</span></label>
                            <input name="email" type="email" class="ehxd-form-input" placeholder="Your email" require>
                        </div>
                        <div class="ehxd-form-group">
                            <label class="ehxd-form-label">Message <span style="color: #e96a6a;">*</span></label>
                            <textarea name="message" class="ehxd-form-input" rows="4" placeholder="Type your message" require></textarea>
                        </div>

                        <button type="submit" class="ehxd-submit-btn">Submit Now</button>
                        <p class="ehxd-response-message" style="margin-bottom: 0; font-size: 12px"></p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>