<?php

return [
    // Minimum character lengths
    'min_title_length'       => 10,
    'min_description_length' => 30,

    // Reject if the same single character/short pattern repeats this many times
    // e.g. "aaaaaa", "asdasdasd"
    'max_repeat_run'         => 4,   // "aaaa" or more
    'max_pattern_repeat'     => 3,   // "asdasdasd" (pattern x3)

    // Max URLs / phone numbers allowed in a single field (anti-spam)
    'max_links'              => 2,
    'max_phones'             => 2,

    // ── Image / photo rules ──────────────────────────────────────
    'image' => [
        'max_kb'      => 2048,                       // 2 MB per image
        'min_width'   => 200,                        // px
        'min_height'  => 200,                        // px
        'max_width'   => 6000,
        'max_height'  => 6000,
        'mimes'       => ['jpg', 'jpeg', 'png', 'webp'],
    ],

    // Banned / abusive words (case-insensitive, whole-word match).
    // Keep lowercase. Add your own as needed.
    'banned_words' => [
        // profanity
        'fuck', 'shit', 'bitch', 'asshole', 'bastard', 'cunt', 'dick', 'pussy',
        'slut', 'whore', 'motherfucker', 'bullshit',
        // sexual content
        'sex', 'sexy', 'sexual', 'nude', 'naked', 'porn', 'xxx', 'adult content',
        'sex chat', 'escort', 'call girl', 'play boy', 'playboy', 'hookup',
        'one night stand', 'naughty', 'erotic', 'hot girl', 'sexy girl',
        'for sex', 'sex room', 'body massage', 'happy ending',
        // scam / spam keywords
        'viagra', 'casino', 'bitcoin doubler', 'forex signals', 'get rich quick',
    ],

    // Obvious junk/placeholder phrases that signal a dummy post
    'junk_phrases' => [
        'test test', 'test data', 'test post', 'this is a test', 'just testing',
        'asdf', 'asdfgh', 'asdfasdf', 'qwerty', 'qwertyuiop',
        'lorem ipsum', 'aaaa', 'bbbb', 'cccc', 'dddd', 'eeee', 'ffff',
        'sample text', 'dummy text', 'dummy data', 'fake data', 'fake post',
        'na na na', 'abc abc', 'jjjj', 'xxxx', 'zzzz', 'blah blah',
        'hello world', 'hi there', 'testing 123', '123 456', 'aaa bbb',
        'nothing here', 'placeholder', 'no description',
    ],

    // Words that are clearly junk when they appear as the entire title/description
    'junk_title_words' => [
        'test', 'testing', 'hello', 'hi', 'hey', 'yo', 'ok', 'okay',
        'check', 'checking', 'nothing', 'idk', 'n/a', 'na', 'none',
    ],

    // Minimum ratio of vowels in a word to detect gibberish (0.0–1.0)
    // Words with vowel ratio below this AND length >= 5 are flagged
    'min_vowel_ratio'  => 0.25,

    // If this fraction of words in a field are gibberish, block the entire field
    // (avoids false positives from one weird word in an otherwise good sentence)
    'max_gibberish_word_fraction' => 0.50,
];
