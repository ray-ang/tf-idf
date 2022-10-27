<?php

require __DIR__ . '/functions.php';

$pain = array(); // term collections document
$pain['presence'] = ['pain', 'hurt', 'ach', 'discomfort', 'hav', 'experienc']; // term collection
$pain['location'] = ['pain', 'hurt', 'ach', 'discomfort', 'where', 'locat', 'area', 'point', 'show', ' part'];
$pain['time'] = ['pain', 'hurt', 'ach', 'discomfort', 'when', 'how long', 'often', 'start', 'begin', 'time', 'now', ' day'];
$pain['degree'] = ['pain', 'hurt', 'ach', 'discomfort', 'rate', 'scale', 'degree', 'how bad', 'better', 'wors'];
$pain['spread'] = ['pain', 'hurt', 'ach', 'discomfort', 'spread', 'radiat', 'area'];

$message1 = 'Are you in pain? If so, which part of your body are you having pain?'; // message
$message2 = 'How bad is your pain? Can you point where the pain is, and does the pain spread to other areas?';
$message3 = 'Where is the pain? Can you show me where it hurts?';

var_dump( tf_idf_total($message1, $pain) ); // presence and location
// var_dump( tf_idf_total($message2, $pain) ); // location, spread and degree
// var_dump( tf_idf_total($message3, $pain) ); // location