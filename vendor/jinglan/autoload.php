<?php
/**
 * Created by PhpStorm.
 * User: op
 * Date: 2018-05-28
 * Time: 19:22
 */

require_once __DIR__ . '/walletapi/WalletRPC.php';

require_once __DIR__ . '/ethereum/EthereumRPC.php';
require_once __DIR__ . '/sms/SMS.php';
require_once __DIR__ . '/sms/juhe/juhesms.php';
require_once __DIR__ . '/sms/api_sdk/vendor/autoload.php';

//require_once __DIR__ . '/bitcoin/JsonRpcClient.php';

require_once __DIR__ . '/bitcoin/src/Client.php';
require_once __DIR__ . '/bitcoin/src/Omnicore.php';
require_once __DIR__ . '/bitcoin/src/MessageTrait.php';
require_once __DIR__ . '/bitcoin/src/ReadOnlyArrayTrait.php';
require_once __DIR__ . '/bitcoin/src/ResponseArrayTrait.php';
require_once __DIR__ . '/bitcoin/src/SerializableContainerTrait.php';
require_once __DIR__ . '/bitcoin/src/BitcoindResponse.php';
require_once __DIR__ . '/bitcoin/src/Exceptions/BitcoindException.php';
require_once __DIR__ . '/bitcoin/src/Exceptions/ClientException.php';

require_once __DIR__ . '/bitcoin/Balance.php';
require_once __DIR__ . '/bitcoin/Unspent.php';

require_once __DIR__ . '/ves/VesRPC.php';

require_once __DIR__ . '/cloud/RTC/ServerAPI.php';

require_once __DIR__ . '/qrcode/Qrcode.php';
require_once __DIR__ . '/qrcode/QRencode.php';
require_once __DIR__ . '/qrcode/QRinput.php';
require_once __DIR__ . '/qrcode/QRinputItem.php';
require_once __DIR__ . '/qrcode/QRbitstream.php';
require_once __DIR__ . '/qrcode/QRsplit.php';
require_once __DIR__ . '/qrcode/QRspec.php';
require_once __DIR__ . '/qrcode/QRtools.php';
require_once __DIR__ . '/qrcode/QRrawcode.php';
require_once __DIR__ . '/qrcode/QRrs.php';
require_once __DIR__ . '/qrcode/QRrsItem.php';
require_once __DIR__ . '/qrcode/QRrsblock.php';
require_once __DIR__ . '/qrcode/QRstr.php';
require_once __DIR__ . '/qrcode/FrameFiller.php';
require_once __DIR__ . '/qrcode/QRmask.php';
require_once __DIR__ . '/qrcode/QRimage.php';