<?php $title = '在线转换bash curl到php curl';?>
<?php
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === "POST")
{
    require 'parse_command_line.php';


    parse_str(file_get_contents('php://input'), $_POST);


//    var_dump($_POST);
//    die();

    // input
    $str = $_POST['input_bash_command'];
    if(!empty($_POST['input_bash_command']))
    {
        $request_info = parse_command_line($str);

        $data_headers = [];
        foreach ($request_info['options']['-H'] as $line)
        {
            $data_headers[] = $line;
        }
        $url = $request_info['arguments'][0];

        if(key_exists('--data', $request_info['options'])){
            $form_data = [];
            parse_str($request_info['options']['--data'], $form_data);
        }

        ob_start();
        require 'code-tmpl.php';
        $output_php_code = ob_get_clean();
    }
    else
    {
        $errors[]='请输入bash curl代码，再点击提交！';
    }
}
?>
<!DOCTYPE html>
<html lang="cn">
<head>
    <meta charset="UTF-8">
    <title><?=$title?></title>
    <style>

        .container{
            width: 80%;
            margin: 0 auto;
        }
        pre {
            display: block;
            padding: 9.5px;
            margin: 0 0 10px;
            font-size: 13px;
            line-height: 1.42857143;
            color: #333;
            word-break: break-all;
            word-wrap: break-word;
            background-color: #f5f5f5;
            border: 1px solid #ccc;
            border-radius: 4px;

            overflow: auto;
            font-family: Menlo,Monaco,Consolas,"Courier New",monospace;

            white-space: nowrap;
        }
        .form-control {
            display: block;
            width: 100%;
            padding: 6px 12px;
            font-size: 14px;
            line-height: 1.42857143;
            color: #555;
            background-color: #fff;
            background-image: none;
            border: 1px solid #ccc;
            border-radius: 4px;
            -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
            box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
            -webkit-transition: border-color ease-in-out .15s,-webkit-box-shadow ease-in-out .15s;
            -o-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
            transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
        }
        label {
            display: inline-block;
            max-width: 100%;
            margin-bottom: 5px;
            font-weight: 700;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .btn {
            display: inline-block;
            padding: 6px 12px;
            margin-bottom: 0;
            font-size: 14px;
            font-weight: 400;
            line-height: 1.42857143;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            -ms-touch-action: manipulation;
            touch-action: manipulation;
            cursor: pointer;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            background-image: none;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        .btn-default {
            color: #333;
            background-color: #fff;
            border-color: #ccc;
        }
        .btn-default:hover, .btn-default:focus, .btn-default.focus, .btn-default:active, .btn-default.active, .open>.dropdown-toggle.btn-default {
            color: #333;
            background-color: #e6e6e6;
            border-color: #adadad;
        }
        .btn-default:active, .btn-default.active, .open>.dropdown-toggle.btn-default {
            background-image: none;
        }
        .btn-default:hover, .btn-default:focus, .btn-default.focus, .btn-default:active, .btn-default.active, .open>.dropdown-toggle.btn-default {
            color: #333;
            background-color: #e6e6e6;
            border-color: #adadad;
        }
        .btn:active, .btn.active {
            background-image: none;
            outline: 0;
            -webkit-box-shadow: inset 0 3px 5px rgba(0,0,0,.125);
            box-shadow: inset 0 3px 5px rgba(0,0,0,.125);
        }
        .btn:focus, .btn:active:focus, .btn.active:focus, .btn.focus, .btn:active.focus, .btn.active.focus {
            outline: thin dotted;
            outline: 5px auto -webkit-focus-ring-color;
            outline-offset: -2px;
        }

        .btn-lg, .btn-group-lg>.btn {
            padding: 10px 16px;
            font-size: 18px;
            line-height: 1.33;
            border-radius: 6px;
        }
        .btn-sm, .btn-group-sm>.btn {
            padding: 5px 10px;
            font-size: 12px;
            line-height: 1.5;
            border-radius: 3px;
        }

        h1 {
            padding-bottom: 9px;
            margin: 40px 0 20px;
            border-bottom: 1px solid #eee;
        }

        .alert-danger {
            color: #a94442;
            background-color: #f2dede;
            border-color: #ebccd1;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }

        footer{
            padding-top: 40px;
            padding-bottom: 40px;
            margin-top: 100px;
            color: #777;
            text-align: center;
            border-top: 1px solid #e5e5e5;
        }
    </style>
</head>
<body>
<div class="container">
    <h1><?=$title?></h1>
    <form method="post">
        <?php foreach ($errors as $error):?>
            <div class="alert alert-danger" role="alert">
                <?=$error?>
            </div>
        <?php endforeach;?>
        <div class="form-group">
            <label for="input-bash-command">请输入从chrome debug bar拷贝的bash脚本</label>
            <textarea style="width: 60%" class="form-control" id="input-bash-command" name="input_bash_command" rows="6"><?=@$_POST['input_bash_command']?></textarea>
        </div>
        <div class="form-group">
            <button class="btn btn-default btn-lg">转换</button>
        </div>
    </form>
    <div class="form-group">
        <label for="input-bash-command">转换成的php代码</label>
        <pre id="input-bash-command" rows="8"><?=@nl2br(str_replace(' ', '&nbsp;', htmlspecialchars($output_php_code)))?></pre>
    </div>
</div>
<footer>
没有版权，请随便用
</footer>
</body>
</html>