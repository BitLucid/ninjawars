<?php
use NinjaWars\core\data\NpcFactory;

// Npc matrix planning document: https://docs.google.com/spreadsheet/ccc?key=0AkoUgtBBP00HdGZ1eUhaekhTb1dnZVh3ZlpoRExWdGc#gid=0
NpcFactory::$data = [
    'firefly'   => [ // Baseline weakest mob
        'name'     => 'Firefly',
        'img'      => 'firefly.png',
        'strength' => 0,
        'stamina'  => 0,
        'damage'   => 0,
        'race'     => 'insect',
        'gold'     => 0,
    ],
    'fireflies' => [
        'name'     => 'Fireflies',
        'img'      => 'fireflies.png',
        'strength' => 0,
        'stamina'  => 1,
        'damage'   => 0,
        'race'     => 'insect',
    ],
    'spider'    => [
        'name'     => 'Spider',
        'img'      => 'spider.png',
        'strength' => 1,
        'stamina'  => 1,
        'speed'    => 1,
        'damage'   => 10,
        'gold'     => 10,
        'race'     => 'insect',
        'ki'       => 1,
    ],
    'pig'       => [
        'name'     => 'Wild pig',
        'short'    => 'is bristling with tusks and wiry hair',
        'stamina'  => 7,
        'strength' => 7,
        'speed'    => 10,
        'damage'   => 20,
        'race'     => 'animal',
    ],
    'viper'     => [
        'name'     => 'Black Viper',
        'race'     => 'animal',
        'strength' => 1,
        'stamina'  => 1,
        'speed'    => 1,
        'ki'       => 1,
        'damage'   => 99,
        'status'   => POISON,
        'gold'     => 30,
        'traits'   => [
            'poisonous',
        ],
    ],
    'kappa'     => [
        'name'      => 'Kappa',
        'strength'  => 30,
        'speed'     => 10,
        'stamina'   => 60,
        'short'     => 'is a reptilian creature with a scooped-out head',
        'race'      => 'kappa',
        'img'       => 'kappa.jpg',
        'gold'      => 40,
        'inventory' => [
            'shell' => '.5',
        ],
        'traits'    => [
            'armored',
        ],
    ],
    'nureonna'  => [
        'name'      => 'Nureonna',
        'strength'  => 30,
        'speed'     => 50,
        'stamina'   => 40,
        'img'       => 'nureonna',
        'race'      => 'yokai',
        'img'       => 'nureonna.jpg',
        'status'    => POISON,
        'inventory' => [
            'charcoal' => 1,
        ],
        'traits'    => [
            'poisonous',
        ],
    ],
    'tengu'     => [
        'name'      => 'Tengu',
        'short'     => 'is a large winged demon',
        'strength'  => 70,
        'speed'     => 20,
        'stamina'   => 100,
        'race'      => 'tengu',
        'inventory' => [
            'tetsubo'=>'.05',
        ],
        'traits'    => [
            'flying',
        ]
    ],
    'ushioni'   => [
        'name'     => 'Ushi-Oni',
        'strength' => 90,
        'stamina'  => 130,
        'speed'    => 50,
        'race'     => 'ushioni',
        'img'      => 'ushioni2.jpg',
        'traits'   => [
            'horned'
        ]
    ],
    'ryu'       => [
        'name'     => 'Ryu',
        'strength' => 210,
        'stamina'  => 200,
        'speed'    => 190,
        'short'    => 'is a serpent-dragon, with the gleam of intelligence in its eyes and the glint of death on its claws',
        'img'      => 'hokusai-dragon.jpg',
        'race'     => 'ryu',
        'traits'   => [
            'armored',
            'rich',
        ],
    ],
] + (
    !defined('DEBUG') || !DEBUG ? [] :
    [
        'peasant2'      => [
            'name'       => 'Peasant',
            'race'       => 'human',
            'img'        => 'fighter.png',
            'strength'   => 5,
            'stamina'    => 5,
            'speed'      => 5,
            'ki'         => 1,
            'damage'     => 1,
            'gold'       => 20,
            'bounty_mod' => 1,
            'traits'     => [
                'villager',
                'sometimes_disguised_ninja',
            ],
            'inventory'  => [
                'kunai'    => '.01',
                'shuriken' => '.01',
            ],
        ],
        'merchant2'     => [
            'name'         => 'Merchant',
            'race'         => 'human',
            'strength'     => 10,
            'stamina'      => 20,
            'speed'        => 10,
            'ki'           => 1,
            'damage'       => 15,
            'gold'         => 50,
            'bounty_mod'   => 5,
            'img'          => 'merchant.png',
            'inventory'    => [
                'phosphor' => '.3',
            ],
            'traits'       => [
                'villager',
                'rich',
            ],
        ],
        'guard2'        => [
            'name'       => 'Guard',
            'short'      => 'is a member of the ashigaru foot soldiers, hired for various tasks',
            'race'       => 'human',
            'strength'   => 30,
            'stamina'    => 30,
            'speed'      => 12,
            'ki'         => 1,
            'damage'     => 0,
            'gold'       => 50,
            'bounty_mod' => 10,
            'img'        => 'guard.png',
            'inventory'  => [
                'ginsengroot' => '.2',
            ],
            'traits'     => [
                'partial_match_strength',
            ],
        ],
        'thief2'          => [
            'name'      => 'Theif',
            'strength'  => 17,
            'stamina'   => 10,
            'speed'     => 10,
            'race'      => 'human',
            'img'       => 'thief.png',
            'inventory' => [
                'shuriken' => '1',

            ],
            'gold'      => 40,
            'traits'    => [
                'steals',
                'escaper',
                'gang',
            ],
        ],
        'monk'          => [
            'name'      => 'Monk',
            'strength'  => 10,
            'stamina'   => 10,
            'speed'     => 10,
            'ki'        => 30,
            'race'      => 'human',
            'inventory' => [
                'prayerwheel' => '.2',
            ],
            'traits'    => [
                'deflection',
                'defensive',
                'self_heal',
            ],
        ],
        'geisha'        => [
            'name'       => 'Geisha',
            'strength'   => 5,
            'stamina'    => 10,
            'speed'      => 15,
            'ki'         => 10,
            'gold'       => 20,
            'bounty_mod' => 50,
            'race'       => 'human',
            'inventory'  => [
                'sake'   => '.2',
                'mirror' => '.01',
                'kimono' => '.01',
                'tessen' => '.01',
            ],
            'traits'    => [
                'packdynamic',
                'guarded',
                'villager',
            ],
        ],
        'koi'           => [
            'name'      => 'Koi',
            'short'     => 'swims through the water',
            'img'       => 'koi.jpg',
            'strength'  => 0,
            'speed'     => 5,
            'stamina'   => 2,
            'damage'    => 1,
            'race'      => 'fish',
            'inventory' => [
                'sushi' => '.3',
            ],
        ],
        'chicken'       => [
            'name'     => 'Chicken',
            'short'    => 'saunters around like it owns the place',
            'strength' => 1,
            'speed'    => 5,
            'damage'   => 0,
            'race'     => 'bird',
        ],
        'bees'          => [
            'name'     => 'Swarm of Bees',
            'short'    => 'swarms and buzzes through the air',
            'strength' => 13,
            'speed'    => 30,
            'damage'   => 6,
            'gold'     => 0,
            'race'     => 'insect',
        ],
        'goat'          => [
            'name'     => 'Goat',
            'short'    => 'chews on anything it can get to',
            'strength' => 10,
            'speed'    => 25,
            'damage'   => 3,
            'race'     => 'animal',
        ],
        'crow'          => [
            'name'     => 'Crow',
            'short'    => 'caws out its disdain',
            'strength' => 3,
            'speed'    => 25,
            'damage'   => 3,
            'race'     => 'bird',
        ],
        'kingfisher'    => [
            'name'     => 'Kingfisher',
            'short'    => 'flashes by on its wings',
            'strength' => 3,
            'speed'    => 30,
            'damage'   => 3,
            'race'     => 'bird',
        ],
        'horse'         => [
            'name'     => 'Horse',
            'short'    => '',
            'strength' => 10,
            'speed'    => 40,
            'stamina'  => 30,
            'damage'   => 10,
            'race'     => 'animal',
        ],
        'ox'            => [
            'name'     => 'Ox',
            'short'    => '',
            'strength' => 50,
            'speed'    => 20,
            'stamina'  => 40,
            'damage'   => 20,
            'race'     => 'animal',
        ],
        'dog'           => [
            'name'     => 'Dog',
            'short'    => 'barks wildly',
            'strength' => 20,
            'speed'    => 20,
            'stamina'  => 10,
            'damage'   => 10,
            'race'     => 'animal',
        ],
        'ghost_dog'    => [
            'name'     => 'Ghost Dog',
            'short'    => 'howls hauntingly',
            'strength' => 15,
            'speed'    => 15,
            'stamina'  => 10,
            'race'     => 'kami',
            'traits'   => 'wispy'
        ],
        'tiger'         => [
            'name'     => 'Tiger',
            'short'    => 'circles in for the kill',
            'strength' => 60,
            'speed'    => 60,
            'stamina'  => 60,
            'damage'   => 60,
            'race'     => 'animal',
        ],
        'basan'         => [ // Uses default race of: creature.
            'name'     => 'Basan',
            'img'      => 'basan.jpg',
            'strength' => 1,
            'stamina'  => 1,
            'speed'    => 1,
            'traits'   => [
                'poisonous',
                'flying',
            ],
        ],
        'kamaitachi'    => [
            'name' => 'Kama-itachi',
            'img'  => 'kamaitachi.jpg',
            'race' => 'yokai',
        ],
        'nuribotoke'    => [
            'name' => 'Nuri-Botoke',
            'img'  => 'nuribotoke.jpg',
            'race' => 'yokai',
        ],
        'hitodama'      => [
            'name'   => 'Hitodama',
            'short'  => 'are spirit orbs of fire',
            'img'    => 'hitodama.gif',
            'race'   => 'kami',
            'traits' => [
                'wispy',
            ],
        ],
        'karakasaobake' => [
            'name'  => 'Karakasa-obake',
            'short' => 'a one-legged umbrella spirit',
            'race'  => 'kami',
        ],
        'kodama'        => [
            'name'  => 'Ko-dama',
            'short' => 'is a tree spirit',
            'race'  => 'kami',
        ],
        'umibozu'       => [
            'name'   => 'Umi-Bozu',
            'short'  => 'are bulbous floating jellyfish',
            'img'    => 'umibozu.jpg',
            'traits' => [
                'wispy',
            ],
        ],
        'shojo'         => [
            'name'  => 'Shojo',
            'short' => 'is a monkey man',
            'race'  => 'yokai',
        ],
        'kamanari'      => [
            'name'  => 'Kamanari',
            'short' => 'is a gateway spirit that inhabits an iron pot',
            'race'  => 'kami',
        ],
        'furaribi'      => [
            'name'   => 'Furaribi',
            'short'  => 'is a flame spirit',
            'race'   => 'kami',
            'img'    => 'furaribi.jpg',
            'traits' => [
                'wispy',
            ],
        ],
        'jorogumo'      => [
            'name'  => 'Jorogumo',
            'short' => 'spider woman',
            'race'  => 'yokai',
            'img'   => 'jorogumo.jpg',
        ],
        'tesso'         => [
            'name'  => 'Tesso',
            'short' => 'rat man',
            'race'  => 'yokai',
            'img'   => 'tesso.jpg',
        ],
        'shoukera'      => [
            'name'  => 'Shoukera',
            'short' => 'is a muscled creature that lies wait in the shadows',
            'race'  => 'yokai',
            'img'   => 'shoukera.jpg',
            'traits'=>[
                'claws',
                'stealthy',
            ]
        ],
        'waira'         => [
            'name'  => 'Waira',
            'short' => 'is a clawed beast',
            'race'  => 'yokai',
            'img'   => 'waira.jpg',
            'traits'=>[
                'claws',
            ]
        ],
        'tsurebeotoshi' => [
            'name'   => 'Tsurube-otoshi',
            'short'  => 'is a fire elemental',
            'race'   => 'kami',
            'img'    => 'tsurubeotoshi.jpg',
            'traits' => [
                'wispy',
            ],
        ],
        'kasha'         => [
            'name' => 'Kasha',
            'img'  => 'kasha.jpg',
            'race' => 'yokai',
        ],
        'yanari'        => [
            'name'  => 'Yanari',
            'short' => 'a group of small demons',
            'img'   => 'yanari.jpg',
            'race'  => 'yokai',
        ],
        'aobouzu'       => [
            'name'  => 'Ao-bouzu',
            'short' => 'is a one eyed monk',
            'img'   => 'aobouzu.jpg',
            'race'  => 'yokai',
            'inventory'=>[
                'prayerwheel'=>'.01',
                
            ]
        ],
        'akashita'      => [
            'name'  => 'Akashita',
            'short' => 'is a storm demon',
            'img'   => 'akashita.jpg',
            'race'  => 'kami',
            'traits'=> [
                'flying',
            ]
        ],
        'kamakiri'      => [
            'name'  => 'Kama-Kiri',
            'short' => 'is a hair eating beast',
            'img'   => 'kamakiri.jpg',
            'race'  => 'yokai',
        ],
        'hakutaku'      => [
            'name'  => 'Hakutaku',
            'short' => 'is a winged lion demon',
            'img'   => 'hakutaku.jpg',
            'race'  => 'yokai',
        ],
        'hainu'         => [
            'name'  => 'Hainu',
            'short' => 'is a winged wolf',
            'race'  => 'yokai',
        ],
        'kitsune'         => [
            'name'  => 'Kitsune',
            'short' => 'is a fox',
            'full_img' => 'kitsune_foxfires.jpg',
            'speed'   => 50,
            'traits' => [
                'stealthy',
                'slippery',
                'polymorphic',
                'trickster',
                'steals',
            ],
            'inventory'=>[
                'charcoal'=>'.33'
            ]
        ],
        'yurei'         => [
            'name'  => 'Yurei',
            'short' => 'is a pale woman with long black hair',
            'full_img'   => 'Tukioka_yositosi-yuurei.jpg',
            'traits' => [
                'polymorphic',
                'undead',
                'wispy',
            ]
        ],
        'oni'           => [
            'name'     => 'Oni',
            'strength' => 25,
            'stamina'  => 5,
            'speed'    => 15,
            'ki'       => 10,
            'short'    => 'a horned demon',
            'img'      => 'hokusai-oni.jpg',
            'race'     => 'oni',
            'inventory' => [
                'tetsubo'=>'.01',
            ],
            'traits'   => [
                'demonic',
                'stealthy',
                'slowing',
                'energy_vampire',
                'horned'
            ],
        ],
        'toad'           => [
            'name'     => 'Toad',
            'strength' => 1,
            'stamina'  => 3,
            'speed'    => 15,
            'short'    => 'a slimy toad',
            'img'      => 'matsuoto-hoji-toad.jpg',
            'race'     => 'animal',
            'inventory' => [
            ],
            'traits'   => [
                'amphibious',
            ],
        ],
    ]
);
