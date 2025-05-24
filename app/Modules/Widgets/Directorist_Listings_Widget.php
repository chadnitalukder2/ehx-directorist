<?php
namespace EhxDirectorist\Modules\Widgets;
use EhxDirectorist\Resources\ListingResource;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

if (!defined('ABSPATH')) {
    exit;
}

class Directorist_Listings_Widget extends Widget_Base {

    public function get_name() {
        return 'ehx-directorist-listings';
    }

    public function get_title() {
        return __('EHX Directorist Listings', 'ehx-directorist');
    }

    public function get_icon() {
        return 'eicon-posts-grid';
    }

    public function get_categories() {
        return ['ehx-directorist'];
    }

    public function get_keywords() {
        return ['directory', 'listings', 'business', 'ehx'];
    }

    protected function register_controls() {
        
        // Content Section
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Content', 'ehx-directorist'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'listings_count',
            [
                'label' => __('Number of Listings', 'ehx-directorist'),
                'type' => Controls_Manager::NUMBER,
                'default' => 6,
                'min' => 1,
                'max' => 50,
            ]
        );

        $this->add_control(
            'columns',
            [
                'label' => __('Columns', 'ehx-directorist'),
                'type' => Controls_Manager::SELECT,
                'default' => '3',
                'options' => [
                    '1' => __('1 Column', 'ehx-directorist'),
                    '2' => __('2 Columns', 'ehx-directorist'),
                    '3' => __('3 Columns', 'ehx-directorist'),
                    '4' => __('4 Columns', 'ehx-directorist'),
                ],
            ]
        );

        $this->add_control(
            'layout_style',
            [
                'label' => __('Layout Style', 'ehx-directorist'),
                'type' => Controls_Manager::SELECT,
                'default' => 'grid',
                'options' => [
                    'grid' => __('Grid', 'ehx-directorist'),
                    'list' => __('List', 'ehx-directorist'),
                ],
            ]
        );

        $this->add_control(
            'show_image',
            [
                'label' => __('Show Image', 'ehx-directorist'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'ehx-directorist'),
                'label_off' => __('Hide', 'ehx-directorist'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_description',
            [
                'label' => __('Show Description', 'ehx-directorist'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'ehx-directorist'),
                'label_off' => __('Hide', 'ehx-directorist'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_contact',
            [
                'label' => __('Show Contact Info', 'ehx-directorist'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'ehx-directorist'),
                'label_off' => __('Hide', 'ehx-directorist'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'directory_builder_id',
            [
                'label' => __('Directory Builder ID', 'ehx-directorist'),
                'type' => Controls_Manager::NUMBER,
                'default' => '',
                'description' => __('Filter by specific directory builder ID (leave empty for all)', 'ehx-directorist'),
            ]
        );

        $this->add_control(
            'order_by',
            [
                'label' => __('Order By', 'ehx-directorist'),
                'type' => Controls_Manager::SELECT,
                'default' => 'created_at',
                'options' => [
                    'created_at' => __('Date Created', 'ehx-directorist'),
                    'updated_at' => __('Date Updated', 'ehx-directorist'),
                    'name' => __('Name', 'ehx-directorist'),
                ],
            ]
        );

        $this->add_control(
            'order',
            [
                'label' => __('Order', 'ehx-directorist'),
                'type' => Controls_Manager::SELECT,
                'default' => 'DESC',
                'options' => [
                    'ASC' => __('Ascending', 'ehx-directorist'),
                    'DESC' => __('Descending', 'ehx-directorist'),
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section
        $this->start_controls_section(
            'style_section',
            [
                'label' => __('Style', 'ehx-directorist'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'card_background_color',
            [
                'label' => __('Card Background Color', 'ehx-directorist'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ehx-directorist-listing-card' => 'background-color: {{VALUE}}',
                ],
                'default' => '#ffffff',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'card_border',
                'label' => __('Card Border', 'ehx-directorist'),
                'selector' => '{{WRAPPER}} .ehx-directorist-listing-card',
            ]
        );

        $this->add_control(
            'card_border_radius',
            [
                'label' => __('Card Border Radius', 'ehx-directorist'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .ehx-directorist-listing-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'card_box_shadow',
                'label' => __('Card Box Shadow', 'ehx-directorist'),
                'selector' => '{{WRAPPER}} .ehx-directorist-listing-card',
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => __('Title Color', 'ehx-directorist'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ehx-directorist-listing-title' => 'color: {{VALUE}}',
                ],
                'default' => '#333333',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => __('Title Typography', 'ehx-directorist'),
                'selector' => '{{WRAPPER}} .ehx-directorist-listing-title',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $listings = ListingResource::getAll($settings['listings_count'], 1, '', [], [], '', '', '', $settings['order_by'], $settings['order']);
        $listings = $listings['data'];
        if (empty($listings)) {
            echo '<p>' . __('No listings found.', 'ehx-directorist') . '</p>';
            return;
        }

        $columns_class = 'ehx-directorist-col-' . $settings['columns'];
        $layout_class = 'ehx-directorist-layout-' . $settings['layout_style'];
        ?>
        
        <div class="ehx-directorist-listings-wrapper <?php echo esc_attr($layout_class); ?>">
            <div class="ehx-directorist-listings-grid <?php echo esc_attr($columns_class); ?>">
                <?php foreach ($listings as $listing) : ?>
                    <div class="ehx-directorist-listing-item">
                        <div class="ehx-directorist-listing-card">
                            
                            <?php if ($settings['show_image'] === 'yes' && !empty($listing->image)) : ?>
                                <div class="ehx-directorist-listing-image">
                                    <img src="<?php echo esc_url($listing->image); ?>" alt="<?php echo esc_attr($listing->name); ?>">
                                </div>
                            <?php endif; ?>

                            <div class="ehx-directorist-listing-content">
                                <div class="ehx-directorist-listing-header">
                                    <?php if (!empty($listing->logo)) : ?>
                                        <div class="ehx-directorist-listing-logo">
                                            <img src="<?php echo esc_url($listing->logo); ?>" alt="<?php echo esc_attr($listing->name); ?>">
                                        </div>
                                    <?php endif; ?>
                                    
                                    <h3 class="ehx-directorist-listing-title">
                                        <a href="<?php echo esc_url($listing->post_url); ?>">
                                            <?php echo esc_html($listing->name); ?>
                                        </a>
                                    </h3>
                                </div>

                                <?php if ($settings['show_description'] === 'yes' && !empty($listing->short_description)) : ?>
                                    <div class="ehx-directorist-listing-description">
                                        <p><?php echo wp_trim_words(esc_html($listing->short_description), 20); ?></p>
                                    </div>
                                <?php endif; ?>

                                <?php if ($settings['show_contact'] === 'yes') : ?>
                                    <div class="ehx-directorist-listing-contact">
                                        <?php if (!empty($listing->phone)) : ?>
                                            <div class="ehx-directorist-contact-item">
                                                <i class="fas fa-phone"></i>
                                                <span><?php echo esc_html($listing->phone); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($listing->email)) : ?>
                                            <div class="ehx-directorist-contact-item">
                                                <i class="fas fa-envelope"></i>
                                                <span><?php echo esc_html($listing->email); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($listing->address)) : ?>
                                            <div class="ehx-directorist-contact-item">
                                                <i class="fas fa-map-marker-alt"></i>
                                                <span><?php echo esc_html($listing->address); ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>

                                <div class="ehx-directorist-listing-footer">
                                    <a href="<?php echo esc_url($listing->post_url); ?>" class="ehx-directorist-view-listing">
                                        <?php echo __('View Details', 'ehx-directorist'); ?>
                                    </a>
                                    
                                    <?php if (!empty($listing->website_url)) : ?>
                                        <a href="<?php echo esc_url($listing->website_url); ?>" class="ehx-directorist-website-link" target="_blank">
                                            <?php echo __('Visit Website', 'ehx-directorist'); ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <?php $this->render_styles(); ?>
        <?php
    }

    private function render_styles() {
        ?>
        <style>
        .ehx-directorist-listings-wrapper {
            margin: 20px 0;
        }
        
        .ehx-directorist-listings-grid {
            display: grid;
            gap: 20px;
        }
        
        .ehx-directorist-col-1 { grid-template-columns: 1fr; }
        .ehx-directorist-col-2 { grid-template-columns: repeat(2, 1fr); }
        .ehx-directorist-col-3 { grid-template-columns: repeat(3, 1fr); }
        .ehx-directorist-col-4 { grid-template-columns: repeat(4, 1fr); }
        
        @media (max-width: 768px) {
            .ehx-directorist-col-2, 
            .ehx-directorist-col-3, 
            .ehx-directorist-col-4 { 
                grid-template-columns: 1fr; 
            }
        }
        
        @media (min-width: 769px) and (max-width: 1024px) {
            .ehx-directorist-col-3, 
            .ehx-directorist-col-4 { 
                grid-template-columns: repeat(2, 1fr); 
            }
        }
        
        .ehx-directorist-layout-list .ehx-directorist-listings-grid {
            grid-template-columns: 1fr;
        }
        
        .ehx-directorist-layout-list .ehx-directorist-listing-card {
            display: flex;
            align-items: flex-start;
        }
        
        .ehx-directorist-layout-list .ehx-directorist-listing-image {
            flex: 0 0 200px;
            margin-right: 20px;
        }
        
        .ehx-directorist-listing-card {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .ehx-directorist-listing-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .ehx-directorist-listing-image img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .ehx-directorist-listing-content {
            padding: 20px;
        }
        
        .ehx-directorist-listing-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .ehx-directorist-listing-logo {
            flex: 0 0 50px;
            margin-right: 15px;
        }
        
        .ehx-directorist-listing-logo img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .ehx-directorist-listing-title {
            margin: 0;
            font-size: 1.2em;
            font-weight: 600;
        }
        
        .ehx-directorist-listing-title a {
            text-decoration: none;
            color: inherit;
        }
        
        .ehx-directorist-listing-title a:hover {
            color: #007cba;
        }
        
        .ehx-directorist-listing-description {
            margin-bottom: 15px;
            color: #666;
        }
        
        .ehx-directorist-listing-contact {
            margin-bottom: 15px;
        }
        
        .ehx-directorist-contact-item {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
            font-size: 0.9em;
            color: #666;
        }
        
        .ehx-directorist-contact-item i {
            margin-right: 8px;
            width: 16px;
            color: #007cba;
        }
        
        .ehx-directorist-listing-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            border-top: 1px solid #f0f0f0;
            padding-top: 15px;
        }
        
        .ehx-directorist-view-listing,
        .ehx-directorist-website-link {
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.9em;
            transition: background-color 0.3s ease;
        }
        
        .ehx-directorist-view-listing {
            background: #007cba;
            color: white;
        }
        
        .ehx-directorist-view-listing:hover {
            background: #005a87;
        }
        
        .ehx-directorist-website-link {
            background: #f0f0f0;
            color: #333;
        }
        
        .ehx-directorist-website-link:hover {
            background: #e0e0e0;
        }
        </style>
        <?php
    }
}