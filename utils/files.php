<?php
function list_dir(string $path)
{
    return array_values(
        array_diff(
            scandir($path),
            array('..', '.')
        )
    );
}
