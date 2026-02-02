<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Profane Words List
    |--------------------------------------------------------------------------
    |
    | Words and slurs to filter from public-facing content like usernames
    | and public aliases. This list is checked against normalized text
    | (lowercase, with common leetspeak substitutions replaced).
    |
    */

    'words' => [
        // Common profanity
        'fuck', 'fucker', 'fucking', 'fucked', 'fucks', 'motherfucker', 'motherfucking',
        'shit', 'shits', 'shitty', 'bullshit', 'horseshit', 'dipshit', 'shithead',
        'ass', 'asshole', 'asses', 'dumbass', 'jackass', 'fatass', 'asshat', 'asswipe',
        'bitch', 'bitches', 'bitchy', 'sonofabitch',
        'damn', 'dammit', 'goddamn', 'goddamnit',
        'cunt', 'cunts',
        'dick', 'dicks', 'dickhead', 'dickwad', 'dickface',
        'cock', 'cocks', 'cocksucker', 'cocksucking',
        'pussy', 'pussies',
        'bastard', 'bastards',
        'slut', 'sluts', 'slutty',
        'whore', 'whores',
        'piss', 'pissed', 'pissing',
        'twat', 'twats',
        'wanker', 'wankers',
        'bollocks',
        'arse', 'arsehole',
        'prick', 'pricks',

        // Racial slurs
        'nigger', 'niggers', 'nigga', 'niggas',
        'darkie', 'darkies', 'darky',
        'spic', 'spics', 'spick', 'spicks', 'spik', 'spiks',
        'wetback', 'wetbacks',
        'beaner', 'beaners',
        'chink', 'chinks', 'chinky',
        'gook', 'gooks',
        'slopehead',
        'zipperhead', 'zipperheads',
        'raghead', 'ragheads', 'towelhead', 'towelheads', 'cameljockey',
        'sandnigger', 'sandnigga',
        'kike', 'kikes', 'heeb', 'heebs', 'hymie', 'hymies',
        'honky', 'honkey', 'honkies', 'peckerwood',
        'whitetrash',
        'wop', 'wops', 'dago', 'dagos', 'guido', 'guidos', 'ginzo',
        'polack', 'polacks',
        'kraut', 'krauts',
        'abbo', 'abbos', 'abo', 'abos', 'boong', 'boongs',
        'redskin', 'redskins', 'injun', 'injuns',
        'halfbreed',

        // LGBTQ+ slurs
        'fag', 'fags', 'faggot', 'faggots', 'faggy',
        'dyke', 'dykes',
        'homo', 'homos',
        'lesbo', 'lesbos',
        'queer', 'queers',
        'tranny', 'trannies', 'shemale', 'shemales', 'ladyboy', 'ladyboys',
        'heshe', 'shehe',
        'sodomite', 'sodomites',
        'battyboy', 'battyman',

        // Disability slurs
        'retard', 'retards', 'retarded', 'tard', 'tards',
        'spaz', 'spazz', 'spastic', 'spazzy',
        'cripple', 'cripples',
        'mongoloid', 'mongoloids',

        // Religious slurs
        'christkiller',
        'goyfucker',

        // Sexual content
        'tits', 'titties', 'titty',
        'boobs', 'boobies',
        'dildo', 'dildos',
        'porn', 'porno', 'pornography',
        'penis', 'penises',
        'vagina', 'vaginas',
        'blowjob', 'blowjobs',
        'handjob', 'handjobs',
        'jizz', 'jizzed',
        'cum', 'cumming', 'cumshot',
        'masturbate', 'masturbating', 'masturbation',
        'erection', 'erections',
        'horny',
    ],

    /*
    |--------------------------------------------------------------------------
    | Leetspeak Substitutions
    |--------------------------------------------------------------------------
    |
    | Common character substitutions used to bypass filters.
    |
    */

    'substitutions' => [
        '0' => 'o',
        '1' => 'i',
        '3' => 'e',
        '4' => 'a',
        '5' => 's',
        '7' => 't',
        '8' => 'b',
        '@' => 'a',
        '$' => 's',
        '!' => 'i',
        '+' => 't',
        '(' => 'c',
        ')' => 'o',
        '|' => 'i',
        '*' => 'u',
        '_' => '',
        '-' => '',
        '.' => '',
    ],
];
