<?php

return [
    'label' => 'Slider 1 (Full width, center align)',
    'description' => 'A full width slider with center aligned text',
    // Fields specific to this variant. This will override the Group's fields.
    'fields' => [
        [
            'name' => 'slides',
            'label' => 'Slides',
            'type' => 'repeatable',
            'fields' => [
                [
                    'name' => 'image',
                    'label' => 'Background Image',
                    'type' => 'image',
                ],
                [
                    'name' => 'title',
                    'label' => 'Main Heading',
                    'type' => 'text',
                ],
                [
                    'name' => 'subtitle',
                    'label' => 'Sub Heading (Above Title)',
                    'type' => 'text',
                ],
                [
                    'name' => 'button_text',
                    'label' => 'Button Text',
                    'type' => 'text',
                ],
                [
                    'name' => 'button_link',
                    'label' => 'Button Link',
                    'type' => 'text',
                ],
                [
                    'name' => 'overlay_opacity',
                    'label' => 'Overlay Opacity (0-100)',
                    'type' => 'number',
                    'default' => 30,
                ],
            ],
        ],
    ],
];
