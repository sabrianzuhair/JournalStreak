<?php

namespace App\Enums;

enum Mood: string
{
    case Happy = 'happy';
    case Great = 'great';
    case Neutral = 'neutral';
    case Sad = 'sad';
    case Angry = 'angry';
    case Tired = 'tired';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    public function emoji(): string
    {
        return match ($this) {
            self::Happy => '😊',
            self::Great => '😄',
            self::Neutral => '😐',
            self::Sad => '😔',
            self::Angry => '😡',
            self::Tired => '😴',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Happy => 'Feeling joyful & grounded',
            self::Great => 'Feeling great & energized',
            self::Neutral => 'Feeling balanced & steady',
            self::Sad => 'Feeling heavy or contemplative',
            self::Angry => 'Processing frustration',
            self::Tired => 'Quiet evening, ready for rest',
        };
    }

    /**
     * Material Symbols icon name used on cards.
     */
    public function icon(): string
    {
        return match ($this) {
            self::Happy => 'mood',
            self::Great => 'sentiment_very_satisfied',
            self::Neutral => 'sentiment_neutral',
            self::Sad => 'sentiment_dissatisfied',
            self::Angry => 'mood_bad',
            self::Tired => 'bedtime',
        };
    }
}
