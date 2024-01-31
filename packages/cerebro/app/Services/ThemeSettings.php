<?php

namespace App\Services;

use App\Models\Sites;
use Illuminate\Support\Facades\Storage;
use Exception;

// 'general' => [
//     'title' => 'Title',
//     'header' => 'Header',
//     'sub_header' => 'Header',
//     'sub_header_color_text' => '#aaa',
//     'sub_header_color' => '#fff',
//     'card_button_text' => 'Card Button Text',
//     'card_button_color_first' => '#fff',
//     'card_button_color_second' => '#ccc',
//     'logo' => 'test.localhost/logo.png',
//     'footer_logo' => '',
//     'favicon' => 'test.localhost/favicon.ico',
//     'hero' => 'test.localhost/hero.png',
//     'hero2' => 'test.localhost/hero2.png',
//     'header_color' => '#FFFFFF',
//     'header_bg_color' => '#FFFFFF',
//     'why_us_bg' => 'test.localhost/why_us_bg.png',
//     'why_us_bg2' => 'test.localhost/why_us_bg2.png',
//     'why_us_icon_fast' => 'test.localhost/why_us_icon_fast.svg',
//     'why_us_icon_easy' => 'test.localhost/why_us_icon_easy.svg',
//     'why_us_icon_secure' => 'test.localhost/why_us_icon_secure.svg',
//     'why_us_bg_color' => '#4B4B4B',
//     'why_us_text_color' => '#FFFFFF',
//     'why_us_cards_text_color' => '#4B4B4B',
//     'why_us_cards_bg_color' => '#FBFBFBFF',
//     'advantages_bg' => '',
//     'comfort_bg' => 'test.localhost/comfort_bg.png',
//     'comfort_bg2' => 'test.localhost/comfort_bg2.png',
//     'comfort_bg3' => 'test.localhost/comfort_bg3.png',
//     'comfort_bg4' => 'test.localhost/comfort_bg4.png',
//     'last_bg' => '',
//     'main_color' => '#2FAF40',
//     'button_color' => '#000000',
//     'button_color_second' => '#69BE60',
//     'step_bar_color' => '#000000',
//     'link_color' => '#000000',
//     'radio_color' => '#000000',
//     'radio_text_color' => '#000000',
//     'footer_background_color' => '#000000',
//     'footer_button_color' => '#000000',
//     'footer_text_color' => '#000000',
//     'comfort_button_color' => '#000000',
// ],

class ThemeSettings
{
    private $themes = [
        'lendingnext' => [
            'general' => [
                'favicon' => 'lendingnext/img/favicon.ico',
                'main_color' => [
                    'label' => 'Main color',
                    'variable' => '--main-color',
                    'value' => '#2FAF40',
                ],
                'main_button_color' => [
                    'label' => 'Main button color',
                    'variable' => '--main-button-color',
                    'value' => '#000000',
                ],
                'button_hover_color' => [
                    'label' => 'Button hover color',
                    'variable' => '--main-button-hover-color',
                    'value' => '#fff',
                ],
                'button_hover_bg_color' => [
                    'label' => 'Button hover background color',
                    'variable' => '--main-button-hover-bg-color',
                    'value' => '#2FAF40',
                ],
                'second_button_color' => [
                    'label' => 'Second button color',
                    'variable' => '--button-color-second',
                    'value' => '#69BE60',
                ],
                'link_color' => [
                    'label' => 'Link color',
                    'variable' => '--link-color',
                    'value' => '#000000',
                ],
                // 'radio_button_color' => [
                //     'label' => 'Radio button background color',
                //     'variable' => '--radio-button-color',
                //     'value' => '#000000',
                // ],
                // 'radio_button_text_color' => [
                //     'label' => 'Radio button text color',
                //     'variable' => '--radio-button-text-color',
                //     'value' => '#000000',
                // ],
                'mobile_menu_bg_color' => [
                    'label' => 'Mobile menu/Why us background color',
                    'variable' => '--mobile-menu-bg-color',
                    'value' => '#444',
                ],
                'mobile_menu_color' => [
                    'label' => 'Mobile menu color',
                    'variable' => '--mobile-menu-color',
                    'value' => '#fff',
                ],

            ],
            'elements' => [
                'EE_header' => [
                    'background-color' => '#ffffff',
                    'CE_nav_link' => [
                        'label' => 'Link color',
                        'color' => '#4b4b4b'
                    ],
                    'CE_header_logo' => [
                        'label' => 'Header logo',
                        'src' => 'lendingnext/img/logo.png'
                    ]
                ],
                'EE_footer' => [
                    'CE_footer_logo' => [
                        'label' => 'Footer logo',
                        'src' => 'lendingnext/img/logo.png',
                    ]
                ],
                'EE_hero' => [
                    'background-image' => 'lendingnext/img/hero.png',
                    'CE_hero_h1' => [
                        'label' => 'H1 color',
                        'color' => '#fff'
                    ],
                    'CE_hero_h2' => [
                        'label' => 'H2 color',
                        'color' => '#fff'
                    ],
                    'CE_hero_h3' => [
                        'label' => 'H3 color',
                        'color' => '#000',
                        'background-color' => '#fff'
                    ],
                    'CE_hero' => [
                        'label' => 'Bottom right image',
                        'background-image' => 'lendingnext/img/hero1.png'
                    ]
                ],
                'EE_wu' => [
                    'background-image' => 'lendingnext/img/street.png',
                    'CE_easy' => [
                        'label' => 'Easy image',
                        'src' => 'lendingnext/img/why_us_icon_easy.svg'
                    ],
                    'CE_fast' => [
                        'label' => 'Fast image',
                        'src' => 'lendingnext/img/why_us_icon_fast.svg'
                    ],
                    'CE_secure' => [
                        'label' => 'Secure image',
                        'src' => 'lendingnext/img/why_us_icon_secure.svg'
                    ],
                    'CE_h2' => [
                        'label' => 'H2 color',
                        'color' => '#fff'
                    ],
                    'CE_card' => [
                        'label' => 'Cards background color',
                        'background-color' => '#FBFBFBFF'
                    ],
                    'CE_text' => [
                        'label' => 'Cards text color',
                        'color' => '#4B4B4B'
                    ],
                    'CE_safe' => [
                        'label' => 'Bottom right image',
                        'src' => 'lendingnext/img/safe.png'
                    ]
                ],
                'EE_hiw' => [
                    'CE_pic1' => [
                        'label' => 'Comfort background 1',
                        'src' => 'lendingnext/img/hiw1.png'
                    ],
                    'CE_pic2' => [
                        'label' => 'Comfort background 2',
                        'src' => 'lendingnext/img/hiw2.png'
                    ],
                    'CE_pic3' => [
                        'label' => 'Comfort background 3',
                        'src' => 'lendingnext/img/hiw3.png'
                    ],
                    'CE_pic4' => [
                        'label' => 'Comfort background 4',
                        'src' => 'lendingnext/img/hiw4.png'
                    ]
                ],
            ],
        ],
        'lendingsource' => [
            'general' => [
                'favicon' => 'lendingsource/img/favicon.ico',
                'main_color' => [
                    'label' => 'Main color',
                    'variable' => '--main-color',
                    'value' => '#69AD74',
                ],
                'main_button_color' => [
                    'label' => 'Button color',
                    'variable' => '--main-button-color',
                    'value' => '#DB4A23',
                ],
                'link_color' => [
                    'label' => 'Link color',
                    'variable' => '--link-color',
                    'value' => '#FFC107',
                ],
                'radio_button_color' => [
                    'label' => 'Radio button color',
                    'variable' => '--radio-button-color',
                    'value' => '#F1DF6A',
                ],
                'radio_button_text_color' => [
                    'label' => 'Radio button text color',
                    'variable' => '--radio-button-text-color',
                    'value' => '#747474',
                ],
            ],
            'elements' => [
                'EE_header' => [
                    'background-color' => '#f5f5f5',
                    'CE_nav_link' => [
                        'label' => 'Link color',
                        'color' => '#707070'
                    ],
                    'CE_header_logo' => [
                        'label' => 'Header logo',
                        'src' => 'lendingsource/img/logo.png'
                    ]
                ],
                'EE_main_button' => [
                    'background-color' => '#69AD74',
                    'color' => '#FFF',
                    ':hover' => [
                        'label' => ':hover effect',
                        'background-color' => [
                            'variable' => '--main-button-hover-bg-color',
                            'value' => '#69AD74',
                        ],
                        'color' => [
                            'variable' => '--main-button-hover-color',
                            'value' => '#FFF',
                        ]
                    ],
                ],
                'EE_footer' => [
                    'background-color' => '#EEEEEE',
                    'CE_footer_link' => [
                        'label' => 'Link color',
                        'color' => '#69AD74'
                    ],
                    'CE_footer_text' => [
                        'label' => 'Text color',
                        'color' => '#A3A3A3'
                    ],
                    'CE_footer_logo' => [
                        'label' => 'Footer logo',
                        'src' => 'lendingsource/img/logo.png'
                    ],
                ],
                'EE_disclosure' => [
                    'background-color' => '#EEEEEE',
                    'CE_footer_text' => [
                        'label' => 'Text color',
                        'color' => '#A3A3A3'
                    ],
                ],
                'EE_hero' => [
                    'background-image' => 'lendingsource/img/hero.jpg',
                    'CE_hero_h1' => [
                        'label' => 'H1 color',
                        'color' => '#5f5f5f'
                    ],
                    'CE_hero_h2' => [
                        'label' => 'H2 color',
                        'color' => '#5f5f5f'
                    ],
                    'CE_hero_span' => [
                        'label' => 'Span color',
                        'color' => '#69AD74'
                    ],
                ],
                'EE_faq' => [
                    'background-color' => '#FFF',
                    'CE_item' => [
                        'label' => 'Item background color',
                        'background-color' => '#F9F9F9',
                    ],
                    'CE_h2' => [
                        'label' => 'Item H2 color',
                        'color' => '#707070',
                    ],
                    'CE_text' => [
                        'label' => 'Item P font color',
                        'color' => '#707070',
                    ],
                ],
                'EE_hiw' => [
                    'background-color' => '#FFF',
                    'CE_h2' => [
                        'label' => 'H2 font color',
                        'color' => '#707070',
                    ],
                    'CE_h3' => [
                        'label' => 'H3 font color',
                        'color' => '#707070',
                    ],
                    'CE_p' => [
                        'label' => 'P font color',
                        'color' => '#707070',
                    ],
                    'CE_card_bg' => [
                        'label' => 'Cards properties',
                        'background-color' => '#F9F9F9',
                    ],
                    'CE_card_num' => [
                        'label' => 'Number square properties',
                        'background-color' => '#69AD74',
                    ]
                ],
                'EE_wu' => [
                    'background-color' => '#F5F5F5',
                    // CE_why_us_card
                    'CE_card' => [
                        'label' => 'Cards properties',
                        'background-color' => '#fff',
                        'color' => '#707070'
                    ],
                    'CE_h2' => [
                        'label' => 'H2 properties',
                        'color' => '#707070'
                    ],
                    'CE_h2_2' => [
                        'label' => 'Big text',
                        'color' => '#FFF'
                    ],
                    'CE_green_box' => [
                        'label' => 'Background image',
                        'background-image' => ''
                    ],
                ],
                'EE_big_button_1' => [
                    'background-color' => '#DB4A23',
                    'color' => '#FFF',
                    ':hover' => [
                        'label' => ':hover effect',
                        'background-color' => [
                            'variable' => '--big-button-1-hover-bg-color',
                            'value' => '#DC4A23',
                        ],
                        'color' => [
                            'variable' => '--big-button-1-hover-color',
                            'value' => '#FFC107',
                        ]
                    ],
                ],
                'EE_big_button_2' => [
                    'background-color' => '#DB4A23',
                    'color' => '#FFF',
                    ':hover' => [
                        'label' => ':hover effect',
                        'background-color' => [
                            'variable' => '--big-button-2-hover-bg-color',
                            'value' => '#DC4A23',
                        ],
                        'color' => [
                            'variable' => '--big-button-2-hover-color',
                            'value' => '#FFC107',
                        ]
                    ],
                ],
                'EE_big_button_3' => [
                    'background-color' => '#DB4A23',
                    'color' => '#FFF',
                    ':hover' => [
                        'label' => ':hover effect',
                        'background-color' => [
                            'variable' => '--big-button-3-hover-bg-color',
                            'value' => '#DC4A23',
                        ],
                        'color' => [
                            'variable' => '--big-button-3-hover-color',
                            'value' => '#FFC107',
                        ]
                    ],
                ],
                'EE_big_button_4' => [
                    'background-color' => '#FFF',
                    'color' => '#707070',
                    ':hover' => [
                        'label' => ':hover effect',
                        'background-color' => [
                            'variable' => '--big-button-4-hover-bg-color',
                            'value' => '#f0f0f0',
                        ],
                        'color' => [
                            'variable' => '--big-button-4-hover-color',
                            'value' => '#FFC107',
                        ]
                    ],
                ],
            ],
        ],
        'loan5000' => [
            'general' => [
                'favicon' => 'loan5000/img/favicon.ico',
                'main_color' => [
                    'label' => 'Main Color',
                    'variable' => '--main-color',
                    'value' => '#277EA7',
                ],
                'main_button_color' => [
                    'label' => 'Button background color',
                    'variable' => '--main-button-color',
                    'value' => '#C72026',
                ],
                'button_hover_color' => [
                    'label' => 'Button :hover Color',
                    'variable' => '--main-button-hover-color',
                    'value' => '#277EA7',
                ],
                'button_hover_bg_color' => [
                    'label' => 'Button :hover background color',
                    'variable' => '--main-button-hover-bg-color',
                    'value' => '#C72026',
                ],
                'footer_bg_color' => [
                    'label' => 'Footer background color',
                    'variable' => '--footer-background-color',
                    'value' => '#B3DDF4',
                ],
                'footer_button_color' => [
                    'label' => 'Footer button color',
                    'variable' => '--footer-button-color',
                    'value' => '#BCE6FD',
                ],
                'footer_text_color' => [
                    'label' => 'Footer text color',
                    'variable' => 'color',
                    'value' => '#277EA7',
                ],
                'mobile_menu_background_color' => [
                    'label' => 'Mobile menu background color',
                    'variable' => '--mobile-menu-background-color',
                    'value' => '#000a3c',
                ],
                'mobile_menu_color' => [
                    'label' => 'Mobile menu color',
                    'variable' => '--mobile-menu-color',
                    'value' => '#fff',
                ],
            ],
            'elements' => [
                'EE_header' => [
                    'background-color' => '#fff',
                    'CE_nav_link' => [
                        'label' => 'Link color',
                        'color' => '#277EA7'
                    ],
                    'CE_header_logo' => [
                        'label' => 'Header logo ',
                        'src' => 'loan5000/img/logo.png',
                    ],
                    'CE_header_logo2' => [
                        'label' => 'Header logo for mobile view',
                        'src' => 'loan5000/img/logo.png',
                    ],
                ],
                'EE_hero' => [
                    'background-image' => 'loan5000/img/header.jpg',
                    'CE_hero_h1' => [
                        'label' => 'H1 color',
                        'color' => '#277EA7'
                    ],
                    'CE_hero_h2' => [
                        'label' => 'H2 color',
                        'color' => '#277EA7'
                    ],
                ],
                'EE_wu' => [
                    'background-image' => 'loan5000/img/whyusBG.jpg',
                    'CE_h2' => [
                        'label' => 'H2 color',
                        'color' => '#277EA7'
                    ],
                    'CE_sublabel' => [
                        'label' => 'Sublabel properties',
                        'background-color' => '#277EA7',
                        'color' => '#fff',
                    ]
                ],
                'EE_advantages' => [
                    
                    'CE_h2' => [
                        'label' => 'H2 color',
                        'color' => '#277EA7'
                    ],
                    'CE_sublabel' => [
                        'label' => 'Sublabel properties',
                        'background-color' => '#277EA7',
                        'color' => '#fff',
                    ],
                    'CE_card' => [
                        'label' => 'Card background-color',
                        'background-color' => '#f6f6f6c6'
                    ],
                    'CE_card_h3' => [
                        'label' => 'Card H3 color',
                        'color' => '#277EA7'
                    ],
                    'CE_card_p' => [
                        'label' => 'Card P color',
                        'color' => '#555'
                    ],
                    'CE_bg' => [
                        'label' => 'Advantages background properties',
                        'background-image' => 'loan5000/img/advantagesBG.png',
                        'background-color' => '#f6f6f6',
                    ],
                ],
                'EE_comfort' => [
                    'CE_bg_color' => [
                        'label' => 'Comfort background color',
                        'background-color' => '#B3DDF4',
                    ],
                    'CE_bg' => [
                        'label' => 'Comfort background image',
                        'background-image' => 'loan5000/img/comfortBG.png',
                    ],
                    'CE_h2' => [
                        'label' => 'H2 properties',
                        'color' => '#277EA7',
                        'background-color' => 'transparent'
                    ],
                    'CE_p' => [
                        'label' => 'P properties',
                        'color' => '#555',
                        'background-color' => 'transparent'
                    ],

                ],
                'EE_last' => [
                    'background-color' => '#fff',
                    'CE_h2' => [
                        'label' => 'H2 color',
                        'color' => '#277EA7'
                    ],
                    'CE_bg' => [
                        'label' => 'Background image',
                        'background-image' => 'loan5000/img/lastBG.jpg',
                    ],
                ],
                'EE_hiw' => [
                    'background-color' => '#f6f6f6',
                    'CE_h2' => [
                        'label' => 'H2 color',
                        'color' => '#277EA7'
                    ],
                    'CE_sublabel' => [
                        'label' => 'Sublabel properties',
                        'background-color' => '#277EA7',
                        'color' => '#fff',
                    ],
                    'CE_card_bg' => [
                        'label' => 'Card background color',
                        'background-color' => '#f6f6f6c6'
                    ],
                    'CE_card_num' => [
                        'label' => 'Num label properties',
                        'background-color' => '#277EA7',
                        'color' => '#fff'
                    ],
                    'CE_card_arrow' => [
                        'label' => 'Arrow color',
                        'color' => '#277EA7'
                    ],
                    'CE_card_h3' => [
                        'label' => 'Card H3 color',
                        'color' => '#277EA7'
                    ],
                    'CE_card_p' => [
                        'label' => 'Card P color',
                        'color' => '#999'
                    ]
                ],
                'EE_faq' => [
                    'background-color' => '#fff',
                    'CE_h2' => [
                        'label' => 'H2 color',
                        'color' => '#277EA7'
                    ],
                    'CE_sublabel' => [
                        'label' => 'Sublabel properties',
                        'background-color' => '#277EA7',
                        'color' => '#fff',
                    ],
                    'CE_item' => [
                        'label' => 'Item background color',
                        'background-color' => '#f8f8f8',
                    ],
                    'CE_icon' => [
                        'label' => 'Toggle icon background color',
                        'background-color' => '#277EA7',
                    ],
                    'CE_text' => [
                        'label' => 'Item text color',
                        'color' => '#277EA7',
                    ]
                ],
                'EE_footer' => [
                    'background-color' => '#B3DDF4',
                    'CE_footer_link' => [
                        'label' => 'Footer menu properties',
                        'background-color' => '#277EA7',
                        'color' => '#BCE6FD'
                    ],
                    'CE_footer_text' => [
                        'label' => 'Footer text color',
                        'color' => '#277EA7'
                    ],
                    'CE_footer_logo' => [
                        'label' => 'Footer logo',
                        'src' => 'loan5000/img/logo.png',
                    ],
                ],
                'EE_copyrights' => [
                    'background-color' => '#277EA7',
                    'color' => '#277EA7'
                ]
            ]
        ],
        'loan10000' => [
            'general' => [
                'favicon' => 'loan10000/img/favicon.ico',
                'main_color' => [
                    'label' => 'Main color',
                    'variable' => '--main-color',
                    'value' => '#EBEBEB',
                ],
                'main_button_color' => [
                    'label' => 'Main button color',
                    'variable' => '--main-button-color',
                    'value' => '#EA8200',
                ],
                'button_color_second' => [
                    'label' => 'Second button color (for :hover effect)',
                    'variable' => '--main-button-color-second',
                    'value' => '#D67700',
                ],
                'step_bar_color' => [
                    'label' => 'Step bar color',
                    'variable' => '--step-bar-color',
                    'value' => '#EA8200',
                ],
                'link_color' => [
                    'label' => 'Link color',
                    'variable' => '--link-color',
                    'value' => '#000000',
                ],
                'radio_button_color' => [
                    'label' => 'Radio button background color',
                    'variable' => '--radio-button-color',
                    'value' => '#000000',
                ],
                'radio_button_text_color' => [
                    'label' => 'Radio button text color',
                    'variable' => '--radio-button-text-color',
                    'value' => '#000000',
                ]
            ],
            'elements' => [
                'EE_header' => [
                    'background-color' => '#fff',
                    'CE_nav_link' => [
                        'label' => 'Menu link color',
                        'color' => '#424242'
                    ],
                    'CE_header_logo' => [
                        'label' => 'Header logo',
                        'src' => 'loan10000/img/logo.svg',
                    ]
                ],
                'EE_hero' => [
                    'background-image' => 'loan10000/img/hero.jpg',
                ],
                'EE_last' => [
                    'CE_bg' => [
                        'label' => 'Background image',
                        'background-image' => 'loan10000/img/lastBG.jpg',
                    ],
                    'CE_text' => [
                        'label' => 'Text color',
                        'color' => '#fff'
                    ]
                ],
                'EE_hiw' => [
                    'CE_pic1' => [
                        'label' => 'Comfort background 1',
                        'src' => 'loan10000/img/comfortBG.png'
                    ],
                    'CE_pic2' => [
                        'label' => 'Comfort background 2',
                        'src' => 'loan10000/img/comfortBG2.png'
                    ],
                    'CE_h2' => [
                        'label' => 'H2 color',
                        'color' => '#4b4b4b'
                    ],
                    'CE_h3' => [
                        'label' => 'H3 color',
                        'color' => '#EA8200'
                    ],
                    'CE_p' => [
                        'label' => 'P color',
                        'color' => '#666'
                    ],
                    'CE_svg' => [
                        'label' => 'Icon fill color',
                        'fill' => '#EA8200'
                    ]
                ],
                'EE_wu' => [
                    'CE_h2' => [
                        'label' => 'H2 color',
                        'color' => '#4b4b4b'
                    ],
                    'CE_p' => [
                        'label' => 'P color',
                        'color' => '#212529'
                    ],
                    'CE_bg' => [
                        'label' => 'Right image',
                        'background-image' => 'loan10000/img/whyusBG.png'
                    ]
                ],
                'EE_advantages' => [
                    'CE_h2' => [
                        'label' => 'H2 color',
                        'color' => '#4b4b4b'
                    ],
                    'CE_card' => [
                        'label' => 'Cards background color',
                        'background-color' => '#4b4b4b'
                    ],
                    'CE_card_p' => [
                        'label' => 'Cards text color',
                        'color' => '#fff'
                    ],
                    'CE_svg' => [
                        'label' => 'Icon fill color',
                        'fill' => '#fff'
                    ],
                    'CE_bg' => [
                        'label' => 'Advantages image',
                        'background-image' => 'loan10000/img/advantagesBG.jpg'
                    ],
                ],
                'EE_faq' => [
                    'CE_h2' => [
                        'label' => 'H2 color',
                        'color' => '#666'
                    ],
                    'CE_text' => [
                        'label' => 'Accordion text color',
                        'color' => '#424242'
                    ]
                ],
                'EE_footer' => [
                    'background-color' => '#4b4b4b',
                    'CE_footer_text' => [
                        'label' => 'Footer text color',
                        'color' => '#fff'
                    ],
                    'CE_footer_logo' => [
                        'label' => 'Footer logo',
                        'src' => 'loan10000/img/logo.svg',
                    ],
                ],
                'EE_copyright' => [
                    'color' => '#fff',
                    'background-color' => '#4b4b4b',
                ]
            ]
        ],
    ];

    public function getSettings($themeName)
    {
        $themeSettings = $this->themes[$themeName] ?? null;

        if ($themeSettings) {
            $this->processThemeSettings($themeSettings);
        }

        return $themeSettings;
    }

    public function mergeSiteSettings($themeName, $siteId)
    {
        $themeSettings = $this->themes[$themeName] ?? null;
        $site = Sites::find($siteId);

        if (!$themeSettings || !$site) {
            throw new Exception('Something went wrong');
        }

        if (empty($site->theme_settings)) {
            $customSettings = $themeSettings;
        } else {
            $customSettings = json_decode($site->theme_settings, true);
        }

        $mergedThemeSettings = $this->recursiveMerge($themeSettings, $customSettings);

        if ($mergedThemeSettings) {
            $this->processThemeSettings($mergedThemeSettings);
        }
        // var_dump($mergedThemeSettings);
        return $mergedThemeSettings;
    }

    private function recursiveMerge($defaultSettings, $customSettings)
    {

        foreach ($customSettings as $key => $value) {
            if (array_key_exists($key, $defaultSettings)) {
                if (is_array($value) && is_array($defaultSettings[$key])) {
                    $defaultSettings[$key] = $this->recursiveMerge($defaultSettings[$key], $value);
                } else {
                    $defaultSettings[$key] = $value;
                }
            }
        }
        return $defaultSettings;
    }

    public function processThemeSettings(&$themeSettings)
    {
        foreach ($themeSettings as $section => &$settings) {
            if ($section === 'general') {
                $this->processGeneralSettings($settings);
            }

            if ($section === 'elements') {
                $this->processElementSettings($settings);
            }
        }
    }

    private function processGeneralSettings(&$generalSettings, $baseUrl = '/')
    {
        foreach ($generalSettings as $key => &$value) {
            if (in_array($key, ['favicon', 'src', 'background-image'])) {
                if (is_string($value) && !empty($value)) {
                    $firstPart = explode('/', $value)[0];
                    if (strpos($firstPart, '.') !== false) { 
                        $url = Storage::disk('sitesResources')->url($value) . '?time=' . time();
                    } else {
                        $url = $baseUrl . $value . '?time=' . time();
                    }
                    $value = $key === 'background-image' ? 'url(' . $url . ')' : $url;
                }
            } elseif (is_array($value)) {
                $this->processArrayValue($value, $baseUrl);
            }
        }
    }
    
    private function processElementSettings(&$elementSettings, $baseUrl = '/')
    {
        foreach ($elementSettings as &$settings) {
            foreach ($settings as $key => &$value) {
                if (in_array($key, ['src', 'background-image'])) {
                    if (is_string($value) && !empty($value)) {
                        $firstPart = explode('/', $value)[0];
                        if (strpos($firstPart, '.') !== false) {
                            $url = Storage::disk('sitesResources')->url($value) . '?time=' . time();
                        } else {
                            $url = $baseUrl . $value . '?time=' . time();
                        }
                        $value = $key === 'background-image' ? 'url(' . $url . ')' : $url;
                    }
                } elseif (is_array($value)) {
                    $this->processArrayValue($value, $baseUrl);
                }
            }
        }
    }
    
    private function processArrayValue(&$array, $baseUrl)
    {
        foreach ($array as $key => &$value) {
            if (in_array($key, ['src', 'background-image'])) {
                if (is_string($value) && !empty($value)) {
                    $firstPart = explode('/', $value)[0];
                    if (strpos($firstPart, '.') !== false) {
                        $url = Storage::disk('sitesResources')->url($value) . '?time=' . time();
                    } else {
                        $url = $baseUrl . $value . '?time=' . time();
                    }
                    $value = $key === 'background-image' ? 'url(' . $url . ')' : $url;
                }
            }
        }
    }
    

}
