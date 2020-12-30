<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .widget-item {
            display: flex;
            flex-flow: column;
            width: 100%;
            flex: 1 0 100%;
            height: 100%;
            z-index: 1;
        }
        .widget-item-header {
            flex-shrink: 0;
            min-height: 40px;
            display: flex;
        }
        .header {
            color: #53565a;
        }
        .header {
            display: table;
            width: 100%;
        }
        #statusContainer.widget-item .header-container {
            justify-content: flex-end;
            width: 100%;
        }
        .header-container {
            display: flex;
            align-items: center;
            min-height: 40px;
            justify-content: space-between;
        }
        .close {
            margin-bottom: auto;
        }

        .close {
            z-index: 0;
            display: inline-block;
            margin-left: auto;
            height: 28px;
        }
        .close svg {
            stroke: #000;
        }
        .close svg {
            transition: .4s ease-in-out;
        }
        .status-container {
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .widget-item-body {
            flex: 1 1 auto;
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
        }
        .threeds-message, .ico {
            text-align: center;
            font: 500 15px/18px Montserrat, sans-serif;
            letter-spacing: normal;
            width: 100%;
            position: relative;
            color: #999;
        }
        .threeds-message, .ico {
            text-align: center;
            font: 500 15px/18px Montserrat, sans-serif;
            letter-spacing: normal;
            width: 100%;
            position: relative;
            color: #fff;
        }
        .success-icon {
            background: url(/images/error.svg) no-repeat;
            display: block;
            width: 81px;
            height: 88px;
            margin: 0 auto 40px;
        }

        .success-icon {
            background: url(/images/error.svg) no-repeat;
            display: block;
            width: 81px;
            height: 88px;
            margin: 0 auto 40px;
            background-position: center;
        }
        .code {
            display: block;
            text-align: center;
            color: #000;
            font-size: 24px;
            line-height: 25px;
            font-weight: 600;
        }

        .code {
            display: block;
            text-align: center;
            color: #000;
            font-size: 24px;
            line-height: 25px;
            font-weight: 600;
        }
        .fail-msg {
            padding: 20px 10px;
            font-weight: 500;
        }
        .widget-item-footer {
            min-height: 70px;
        }
        .btn.btn-bor {
            background: transparent;
            border: 2px solid #1B96FE;
            color: #1B96FE;
        }
        .btn {
            display: block;
            width: 50%;
            padding: 15px;
            margin-right: auto;
            margin-left: auto;
            cursor: pointer;
            color: #fff;
            font: 600 16px/16px Montserrat, sans-serif;
            text-align: center;
            text-transform: uppercase;
            border: none;
            text-decoration: none;
            transition: all ease-in-out 0.3s;
            border-radius: 4px;
        }

        input, textarea, select, button {
            font: 100% Montserrat, Helvetica, sans-serif;
            vertical-align: middle;
            -webkit-appearance: none;
            -webkit-border-radius: 0;
        }
    </style>
</head>
<body>
    <div id="statusContainer" class="widget-item success">
        <div class="widget-item-header">
            <div class="header">
                <div class="header-container">
                    <span class="close">
                        <svg width="28" height="28">
                            <use xlink:href="#close"></use>
                        </svg>
                    </span>
                </div>
            </div>
        </div>
        <div class="widget-item-body status-container">
            <!-- ko if: !threeDSPopupButtonIsVisible() -->
            <div class="ico">
                <div class="success-icon"></div>
                <strong class="code">{{ $message ?? 'Ошибка, обратитесь к оператору.' }}</strong>
                <p class="fail-msg" style="display: none;"></p>
            </div>
            <!-- /ko -->
            <!-- ko if: threeDSPopupButtonIsVisible() --><!-- /ko -->
        </div>
    </div>
</body>
</html>