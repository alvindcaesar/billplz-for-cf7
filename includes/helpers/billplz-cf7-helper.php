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

function bcf7_get_url()
{
  $live    = "https://www.billplz.com/api/v3/bills";
  $sandbox = "https://www.billplz-sandbox.com/api/v3/bills";

  $url = ("Live" == bcf7_get_mode()) ? $live : $sandbox;

  return $url;
}

function bcf7_get_api_key()
{
  $live    = base64_encode(bcf7_api_option("bcf7_live_secret_key"));
  $sandbox = base64_encode(bcf7_api_option("bcf7_sandbox_secret_key"));

  $api_key = ("Live" == bcf7_get_mode()) ? $live : $sandbox;

  return $api_key;
}

function bcf7_get_collection_id()
{
  $live    = bcf7_api_option("bcf7_live_collection_id");
  $sandbox = bcf7_api_option("bcf7_sandbox_collection_id");

  $collection_id = ("Live" == bcf7_get_mode()) ? $live : $sandbox;

  return $collection_id;
}