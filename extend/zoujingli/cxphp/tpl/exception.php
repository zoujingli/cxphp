<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>系统发生错误</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <style>
        html, body, fieldset {

            display: block;
        }

        fieldset {
            margin-top: 20px;
            border: 1px solid rgba(82, 57, 167, 0.6);
        }

        fieldset b {
            display: block;
            padding: 10px 3px 5px 2px;
        }

        fieldset legend {
            padding: 0 5px;
        }

        pre p {
            margin: 0;
            padding: 0 6px;
            overflow: hidden;
            line-height: 28px;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        pre p:nth-of-type(odd) {
            background: rgba(0, 126, 255, 0.13)
        }

        pre p:nth-of-type(even) {
            background: rgba(255, 204, 0, 0.07);
        }
    </style>
</head>
<body>
<?php if (isset($e)) { ?>
    <fieldset>
        <legend>Exception</legend>
        <b><?php echo "<b>{$e->getMessage()}</b>"; ?></b>
        <pre><?php
            $pe = $e;
            do {
                printf("<p> %s:%d %s (%d) [%s] </p>", basename($pe->getFile()), $pe->getLine(), $pe->getMessage(), $pe->getCode(), get_class($pe));
            } while ($pe = $pe->getPrevious());
            ?></pre>
    </fieldset>
    <fieldset>
        <legend>Call Stack</legend>
        <pre><?php
            foreach ($e->getTrace() as $index => $trace) {
                printf("<p> %d. at %s->%s() in %s:%s</p>", $index + 1, $trace['class'], $trace['function'], $trace['file'], $trace['line']);
            } ?></pre>
    </fieldset>
<?php } else { ?>
    <h3>系统发生错误</h3>
<?php } ?>
</body>
</html>