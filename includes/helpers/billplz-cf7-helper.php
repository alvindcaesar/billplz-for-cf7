<?php

function bcf7_general_option($key = '', $default = false)
{
  $value = ! empty(get_option('bcf7_general_settings')[$key]) ? get_option('bcf7_general_settings')[$key] : $default;

  return $value;
}

function bcf7_api_option($key = '', $default = false)
{
  $value = ! empty(get_option('bcf7_api_options')[$key]) ? get_option('bcf7_api_options')[$key] : $default;

  return $value;
}

function bcf7_get_mode()
{
  $mode = ("1" == bcf7_general_option("bcf7_mode")) ? "Test" : "Live";
  return $mode;
}