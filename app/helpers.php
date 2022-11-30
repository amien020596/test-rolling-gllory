<?php

function get_format_rating($input_rating)
{
  $smallest_fractional_unit = 0.5;
  $mod = fmod($input_rating, $smallest_fractional_unit);

  if (round($mod, 2) <= round(0.2, 2)) {
    $final_rating = $input_rating - $mod;
  } else {
    $val_interval = $smallest_fractional_unit - $mod;

    $final_rating = $input_rating + $val_interval;
  }

  return $final_rating;
}
