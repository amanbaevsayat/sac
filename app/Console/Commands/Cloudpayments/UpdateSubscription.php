<?php

namespace App\Console\Commands\Cloudpayments;

use App\Models\Subscription;
use App\Services\CloudPaymentsService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class UpdateSubscription extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cloudpayments:update:subscription';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Команда для обновления подписок';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        \Log::info('Start - UpdateSubscriptionCommand');
        $cloudPaymentsService = new CloudPaymentsService();

        // $subscriptionIds = [
        //     'sc_74bf31649e1e6138b418c40fd5f7f',
        //     'sc_a2ebc94fd5b47d2e334040f5cb454',
        //     'sc_67f486025f7f5817cb9d9617efc4b',
        //     'sc_8bcc48cd9f626a61036430bda7d6a',
        //     'sc_dc48c3f20f7bf79ced4b7ce9a0ffe',
        //     'sc_eb774f59d87555d6402370472a980',
        //     'sc_c6c301694240273a62bca947d7010',
        //     'sc_d510460dfe30191b744ca80b1751a',
        //     'sc_ca9d4a9c2124e1f0c6a00196170aa',
        //     'sc_cd78dc5d08a8796abc5ebc34b625c',
        //     'sc_594032e1d9ab87026e8cf1f7056eb',
        //     'sc_9dbb60713205dd0be2e38cc3489cb',
        //     'sc_d4d776b00b1716cfa73497c0429af',
        //     'sc_9e75d0d15ce0fed1d0e0b5b21e619',
        //     'sc_8c90b9ef96ce4d25300c71972b9c2',
        //     'sc_c00124c9465c9e45864f1cacafbbd',
        //     'sc_387eda85c44db997ea3f71d93ddf4',
        //     'sc_539db3e51ee179ffdf96d689183f4',
        //     'sc_e2ce52e3ff9e40810cddac7d2d03e',
        //     'sc_a7dbe01d4ceebede8d84547a1c687',
        //     'sc_1e0a3e2fc30406999d76ba856095f',
        //     'sc_95e5c0d20d967d5b1a11e2fe6e101',
        //     'sc_79f7824c22fb8c47995d768acb351',
        //     'sc_92dcacb985a2ba3f8541117c190b0',
        //     'sc_9b5926864b615cdc8a587ede56698',
        //     'sc_3ac2abd84166fe95762d2a06be083',
        //     'sc_eb9b959ae245d0d1a69a2a3f991d6',
        //     'sc_2b2abd4696c206b048a6d48fff3fb',
        //     'sc_f5182b87e423bee439977bc54b82a',
        //     'sc_0c392597e5683aea315e2b84fa8d2',
        //     'sc_4e4a265604afe8dc22e56fd06ef08',
        //     'sc_60f82125bfffb2372013921bb0eaf',
        //     'sc_7fac26f75f1b7904fc4f7a27ff296',
        //     'sc_f25161f9fd8ec9c1be5e5d1fd7d48',
        //     'sc_93ee96b9e0990a6d5bb80bcb0f5d4',
        //     'sc_3d947f8ef919f2ba4a7b865c99d70',
        //     'sc_ff82f60b314d5698d2126f34c365a',
        //     'sc_61106a56f7ed9c0d0615379942701',
        //     'sc_603d977961cab8701e7c2a73607e8',
        //     'sc_761c3b4b8520051cb6efc0e45e523',
        //     'sc_11aecaabd499e0706b38d71bf6a82',
        //     'sc_29dbab5672c4086d681e4ee7f0f91',
        //     'sc_63424232abeecd9b60d3e15c9136e',
        //     'sc_6c59cc7a9b6f0b12c7dd4a7f85bd1',
        //     'sc_fe9cc90a0f20d9837cb6a06da4506',
        //     'sc_0848ca5e653895d8c6e4b6a5a27f0',
        //     'sc_06ee9ff53bafcf1484b2abc0e33ed',
        //     'sc_c0cb855ab15fb95eb6c3aee12ae24',
        //     'sc_4aa7c288534597b5752dd78a5e09f',
        //     'sc_0d17367f142c1827ad204a07aae52',
        //     'sc_a02ecca75a756250a1c71dc05816b',
        //     'sc_30a18b9eac0c7f9ded2dfc7830d74',
        //     'sc_711b681d47b4138e882733d2bf34e',
        //     'sc_f6135fece84d2bdbdbb7ee1fc6778',
        //     'sc_8b555d235de2bfbe150864bccfabc',
        //     'sc_b6078a0281ebcf4fbd8fa813a1225',
        //     'sc_d368e92ca7d40c0f235fa858ea668',
        //     'sc_a06398be2022c73fa2f0a3dbd857f',
        //     'sc_d49e2e8d403a685b6023bbca987a1',
        //     'sc_5b11b86c2682d213ae4dd9fe90343',
        //     'sc_bc61b250de147aa3c556026449a6e',
        //     'sc_62be174113a7d1d92a0651f9b9140',
        //     'sc_b7c82dcdd1c1564df91c6e08fb526',
        //     'sc_7c46a832c34dfcf2308075d944a67',
        //     'sc_8c476fd458ef46b772c375b59634b',
        //     'sc_38e9cda820e52eae1593ffc30eb93',
        //     'sc_624a31b55cf8646203edc1eef26d8',
        //     'sc_4dae30142c95ba75eb4ba520f69b8',
        //     'sc_d4722754ea705e274bec81554fcee',
        //     'sc_278daf74d37c80bb12e5d3367908d',
        //     'sc_3f96d06ddb4704d24c1719a93ea45',
        //     'sc_5a351c7c06c01acbf6657b72d730c',
        //     'sc_f2aa7945be9492a958837fd48821d',
        //     'sc_d58b08810ec4aee477b8b02288e83',
        //     'sc_96b9d6d5569953bab4e04ca982aa1',
        //     'sc_1191528191871b35d1f3a78d68cf6',
        //     'sc_8868f99f0fad34c6a8bbd04e748af',
        //     'sc_74fcd1c6067c7c649cea7fe45f249',
        //     'sc_1de0767c8948692e69b00aa645bd6',
        //     'sc_bb57fa7fda945e9792dc53f61066e',
        //     'sc_5e7e2a7b5270aec18cce3682d2785',
        //     'sc_987ff29eba7d214e17cc52d9bf8ac',
        //     'sc_76ee3ed63bc91dd637547ddeb88d4',
        //     'sc_dc17bd8c4a25b18476412c30b56af',
        //     'sc_e24a2f5829e314f21e7540b7efedf',
        //     'sc_b4850438274f4e11286c11b0cfab3',
        //     'sc_c51a1f111961a511681effd0d64f0',
        //     'sc_e624e59024ffe6d10bf97322fd1be',
        //     'sc_c88beee93c6910d70cc29169974a9',
        //     'sc_5649cca1746c61f5681bcf08bdf9b',
        //     'sc_43557cdb787c553be62ad2f2ef248',
        //     'sc_5a0564aa572dff9d8db228bd2f130',
        //     'sc_a7335e6853a0ca1c4cdd91ce3df2d',
        //     'sc_d90ef6ca262f32db0ebd9f5e20404',
        //     'sc_e9d376d749511674c26d3fb792e7c',
        //     'sc_d0196b6367da752ebf17e12aed642',
        //     'sc_4637587e2f6c627b038125269dfd9',
        //     'sc_30d9d5258e315fd324267d2d38dab',
        //     'sc_7486453937373d9dda72143d7cd02',
        //     'sc_aef3d148fbdeb5a8af04ed513bbb0',
        //     'sc_68bf3cdf91dc4ce805c05ed3e46c0',
        //     'sc_f4939016a9745fd4bd3a7b657f84c',
        //     'sc_c575854c3c068be84dc84e74e4509',
        //     'sc_acc94ec2b5f3d8256cd472bfba319',
        //     'sc_e836b5f98b59276927db57750ca34',
        //     'sc_86248eabbe09edf71c4777a3a58cc',
        //     'sc_a1e1c7bd1832d73cf4e4dd1a741b1',
        //     'sc_c4e6501cfe9aaab29a9f75b533980',
        //     'sc_7ceda23360aa948c3e8ab07a466cd',
        //     'sc_e6ec55075b5df3496a4d7f60cc379',
        //     'sc_29d44ec53a8e54657512f543d3126',
        //     'sc_fee3e40379cf3784341065fff2a21',
        //     'sc_f2b5ec5861363b1a349fb5f9c1cb9',
        //     'sc_8f542576d613bd37c7410161b6985',
        //     'sc_ed537e9a1ea4a9ea7364355fec13c',
        //     'sc_cff8d4cb13658730c25e68abd39ae',
        //     'sc_8c754eb90eb58c0b537cb1aaa91bb',
        //     'sc_a66db6b6491f121e4e2e7db849494',
        //     'sc_5cf5aaf83c779f77125ee4a28e6ae',
        //     'sc_b467a5a1d1332e21a397efa33523e',
        //     'sc_e321292785b66ed60a72b3c854878',
        //     'sc_ca7c4ef2d63d61a9ebb65928a7e76',
        //     'sc_c3b27d96f72fc3a16a10d40d67153',
        //     'sc_f3068bd6facada946d3ed4ffa77d5',
        //     'sc_287437bec16bc81b918f135f75801',
        //     'sc_55f3cd59152ff02b3bc407c86e9d1',
        //     'sc_c4c914a343bacf3d776b1e09ff624',
        //     'sc_426f494a892751fb9efc15edefef2',
        //     'sc_96fd1c0cf771f5337aec05a0d1533',
        //     'sc_d16972d5444a50232be4ebbca9cc7',
        //     'sc_64e3c7affa7ee77f3b34da14051ba',
        //     'sc_398d53d318b6c721e8b0902a012a8',
        //     'sc_0b8d49ce9adad71b1abae51bd1f5d',
        //     'sc_59329b0463f335b90cb7274eb6c48',
        //     'sc_e8a9455e14ae70e8c04f173e0313c',
        //     'sc_ab440b9a98cfcf8f8943164fcde83',
        //     'sc_a2388864b5995d37c70efe37fcc4d',
        //     'sc_214e406c7a22d4780ed8ca8b86d4e',
        //     'sc_c61c3dee1d72398ec3fdd8ce89f4c',
        //     'sc_a1156573d4c74733bcbd3891fba7f',
        //     'sc_8abfe4cdaf528b70f09569f29e315',
        //     'sc_94cb6032559252ab77dfd475c134f',
        //     'sc_3a9dde24e0f18f69630a7b661f1d3',
        //     'sc_262597ff7a96798e84eafad7ce7cd',
        //     'sc_e702a4299a12cb34f39c3fe56fb16',
        //     'sc_ef6ca1e954b6f5ac550845cd997ea',
        //     'sc_5ab3efc5a540fc2541ae136b8838d',
        //     'sc_b2696dca93e51c9eccff47d172f60',
        //     'sc_59800fdf61b5ed3ffc362e6165f7c',
        //     'sc_614a3d6a74cde8d2f03e8d565e60d',
        //     'sc_1917d6e4a48deb9e9ad4719191172',
        //     'sc_5408e4acc6bc51320fa059dc4b6e6',
        //     'sc_28e37a85f83c9ceeec195b0e21d2f',
        //     'sc_0224e7bc7159148aa7eadd80802b5',
        //     'sc_f8a3a8af1506d0bde0ef61c058c2f',
        //     'sc_940f63de4ad5facddaf6882a1eb89',
        //     'sc_6b5e66233bd055079c830f7841a1d',
        //     'sc_458afdc5ff7d56c2fc44fb341b5df',
        //     'sc_06ddfbc5f2bd5175fa3dc0b95a790',
        //     'sc_7f55d37fc7a3ce307a16ec5128d99',
        //     'sc_2be658f719db4762926352fbd9435',
        //     'sc_36d5b1acd25915deefee6403bbd87',
        //     'sc_b779fdb9922e52deb410508625dfd',
        //     'sc_5cdb1b61b314ec73c00fd29358596',
        //     'sc_1b7b06c6c9ceda1b7629a211fc420',
        //     'sc_e3e89f0143d4574946ba7ccdb0c0c',
        //     'sc_084aeb87fde34f1d20f7735c21cfe',
        //     'sc_50d74fb659b9dfb9dcda01b8926c3',
        //     'sc_209574a38f53bcb3602fa5468a1d9',
        //     'sc_e33ed6bb7935883bffc67b837868d',
        //     'sc_114ee6a0bb9bb60bdcc6a4a6ba6ba',
        //     'sc_0447c56ba4ce5c856d488d589a316',
        //     'sc_3ffffa2ebe0ee29ee77479b583578',
        //     'sc_f1bf3583ceef6a65d1750daddc5ae',
        //     'sc_68e13cf4aaf1b8bb8bef621a693df',
        //     'sc_c0fed67d700766f326dfe496946bf',
        //     'sc_f8bfd3ec412a7a5f4cd8fb5adaac0',
        //     'sc_40433710685368413f6317e042bf4',
        //     'sc_ab96e5a64807767909d3e41e77517',
        //     'sc_68c7c25449772d9757e32897f7607',
        //     'sc_46cda7f0e2c3f4f6f4c8181cd3fd1',
        //     'sc_6772cdbb09e3e0f9533dda96484f5',
        //     'sc_ff4d07e7592b995824341911babb7',
        //     'sc_ce21a5952a14a8b9afb67eed908b8',
        //     'sc_44bafe6e0eaec53d0a289cd546f6c',
        //     'sc_dd62ebc339e90daefe9440605889c',
        //     'sc_b6c24b80a99aa932b2adb001a072b',
        //     'sc_47a1e14b34c96bfca75ef8650aab5',
        //     'sc_1f59b6ef87fb7f5a2f222d2cf4100',
        //     'sc_f748b9e5e474cf2697459ad146ed2',
        //     'sc_edf2982f031785a39c559682fe430',
        //     'sc_6b7132922734c806d2ee20fd46d11',
        //     'sc_0e46b2ec93d94c35059a56d2f7d96',
        //     'sc_bdd1e0301aedaeb402b2f4ec342e6',
        //     'sc_4cf5a8f1c818e45d37c32d2d49791',
        //     'sc_19e03ca2be035f47c5306cdfb36cf',
        //     'sc_2e10c4036fec97cd523f3d451c251',
        //     'sc_5a8a06c3c18074d8cf59dc9ab54bf',
        //     'sc_0c5024ea33ba73bbb6f433aed1180',
        //     'sc_47066b60b6ae6f13b61c229aec83c',
        //     'sc_779c697f3dfc79a2d9d1f58537561',
        //     'sc_adfa3dbb1f232ec5ad37419696aa4',
        //     'sc_a8e5c48b484b0ef986a9eb0246dd8',
        //     'sc_6c8bfa6c575a0f5c63dca9d24d965',
        //     'sc_ab97cf2ba7a8bd5c654c86ccb2428',
        //     'sc_355fd2f528c156ed317ccb7e783d5',
        //     'sc_ec87829630d30bb7ae594c9f2bc4c',
        //     'sc_3dfa092a8f4713d4b260e1368e7b3',
        //     'sc_ffc74193660d607940452fcaf1f43',
        //     'sc_d3781bbdce85a1b0ee152f3315870',
        //     'sc_5e0d29524bd129ec0b88ced8fa703',
        //     'sc_8226f4b39247f463a5475a04818c6',
        // ];

        // foreach ($subscriptionIds as $id) {
        //     $subscription = Subscription::where('cp_subscription_id', $id)->first();

        //     if (!isset($subscription)) {
        //         $response = $cloudPaymentsService->getSubscription($id);
        //         \Log::info('Не нашел пописку в базе. Cloudpayment ID: ' . $id . '. Status: ' . $response['Success']);
        //         if ($response['Success']) {
        //             $subscription = Subscription::whereHas('customer', function (Builder $query) use ($response) {
        //                 $query->where('phone', $response['Model']['AccountId']);
        //             })->first();

        //             if (!isset($subscription)) {
        //                 \Log::info('Подписка не найдена. ID: ' . $id);
        //             } else {
        //                 $subscription->update([
        //                     'cp_subscription_id' => $id
        //                 ]);
        //             }
        //         } else {
        //             \Log::info('Ошибка при поиске подписки. Subscription ID: ' . $id);
        //         }
        //     }
        // }

        $subscriptions = Subscription::whereNotNull('cp_subscription_id')->wherePaymentType('cloudpayments')->get();

        foreach ($subscriptions as $subscription) {
            $response = $cloudPaymentsService->getSubscription($subscription->cp_subscription_id);
            // if ($subscription->cp_subscription_id == 'sc_cf4bf287186d18c652f97f7d3f11a') {
            //     dd($response);
            // }
            if ($response['Success'] === true) {
                $data = $subscription->data;
                $endedAt = Carbon::parse($response['Model']['NextTransactionDateIso']);
                $data['cloudpayments'] = $response['Model'];

                $subscription->update([
                    'status' => $subscription->status != 'frozen' ? Subscription::CLOUDPAYMENTS_STATUSES[$response['Model']['Status']] : 'frozen',
                    'data' => $data,
                    'ended_at' => $endedAt,
                ]);
                $subscription->customer->update([
                    'email' => $response['Model']['Email'],
                ]);


                // Start
                // $cloudPaymentsService = new CloudPaymentsService();
                // $data = [
                //     'Id' => $subscription->cp_subscription_id,
                //     'CustomerReceipt' => [
                //         "Items" => [
                //             [
                //                 "label" => $subscription->product->title,
                //                 "price" => $subscription->price,
                //                 "quantity" => 1.00,
                //                 "amount" => $subscription->price,
                //                 "object" => $subscription->product->id,
                //             ]
                //         ],
                //         "taxationSystem" => 0,
                //         "email" => $subscription->customer->email ?? '',
                //         "phone" => $subscription->customer->phone,
                //         "isBso" => false
                //     ]
                // ];
                // $cloudPaymentsService->updateSubscription($data);
                // End
                \Log::info('Cloudpayment Subscription update ID: ' . $subscription->id . '. EndedAt: ' . $endedAt->format('Y-m-d H:i:s'));
            } else {
                \Log::info('Ошибка при поиске подписки. Subscription ID: ' . $subscription->id);
            }
        }

        \Log::info('End - UpdateSubscriptionCommand');
    }
}
