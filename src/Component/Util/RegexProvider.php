<?php

namespace Component\Util;


abstract class RegexProvider {
    public static $baseNameRegex = "/^[A-Za-zĘęÓóĄąŚśŁłŻżŹźĆćŃń0-9@_\/\+\.\s]+$/";

    public static $extendRegex = "/^[A-Za-zĘęÓóĄąŚśŁłŻżŹźĆćŃń0-90-9@_\/\+\.\!\?,\s]+$/";
}
