<?php

function render_input($data = array())
{
    include(__dir__ . "/../partials/input_field.php");
}

function render_button($data = array())
{
    include(__DIR__ . "/../partials/button.php");
}

function render_table($data = array())
{
    include(__DIR__ . "/../partials/table.php");
}

function render_card($data = array(), $tabledata = [], $includesCreate = true)
{
    include(__DIR__ . "/../partials/card.php");
}