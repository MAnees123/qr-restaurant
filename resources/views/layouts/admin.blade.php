@php
    $theme = auth()->check() ? (auth()->user()->theme ?? 'default') : 'default';
    if (!view()->exists("themes.{$theme}.layout")) {
        $theme = 'default';
    }
@endphp
@extends("themes.{$theme}.layout")
