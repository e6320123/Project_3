<?php

use Illuminate\Database\Seeder;
use App\News_list;

class News_list_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        News_list::truncate();
        News_list::create(["fname" => "dxi","imgName" => "1","title" => '《勇者鬥惡龍XI S》體驗版近期公開！《魔導少年》真島浩設計服裝同步發表',"content" => '日本 Square Enix 預定於 2019 年 9 月 27日在 Nintendo Switch 主機上推出，將之前在 PS4／N3DS 等平台推出的人氣RPG《DQXI》重新加強移植的《勇者鬥惡龍XI S 尋覓逝去的時光 Definitive Edition》（ドラゴンクエストXI過ぎ去りし時を求めてS），宣布將於近期推出遊戲體驗版讓玩家們搶先試玩！本作為一款將之前於 PS4／N3DS 主機上推出的《DQXI》給重新移植到 Switch主機上重新推出，並追加角色語...']);
        News_list::create(["fname" => "zinyu","imgName" => "2","title" => '【評測】與其奔波勞碌廝殺拚命，不如回《神鵰俠侶2》做比翼雙飛',"content" => '由中國完美時空出品的重大IP製作《神鵰俠侶2》，歷經近一年的封測與調校，於7月底正式開啟內地全平台公測。細緻畫質任君選擇與之前版本不同的，主要在於畫面細膩度與3D視角的自由移動。目前大多數的手遊雖然已經可以自由切換 ...']);
        News_list::create(["fname" => "bee","imgName" => "bee","title" => '人氣PC圖像益智猜謎問答手機移植版《Koongya CatchMind》8月8日韓國雙平台同步推出',"content" => '韓國 Netmarble（網石遊戲）預定於 2019 年在韓國手機平台上推出的圖像益智猜謎問答遊戲《Koongya Catch Mind》（쿵야 캐치마인드），正式宣布將決定於8 月 8 日起在韓國 App Store／Google Play 推出上架！本作為一款將 200....']);
        News_list::create(["fname" => "cod","imgName" => "1","title" => '《決勝時刻：現代戰爭》多人對戰宣傳預告曝光！跨平台連線公測日期同步公布',"content" => '由 Infinity Ward 所開發的《決勝時刻：現代戰爭》在今年 5 月首次曝光後便獲得了不小的關注，而隨著正式的發售日期已確立為 10 月 25日後，更是讓不少系列粉絲更為期待。而官方近期也釋出了新的遊戲展示預告，讓玩家一窺該作的多..']);
        News_list::create(["fname" => "wc3","imgName" => "wc3","title" => '《魔獸爭霸 3》重製版新資訊曝光！索爾、泰蘭妲及多個單位高畫質遊戲模組亮相',"content" => '上曝光後，不少死忠玩家便都在等待該款作品再次以高品質的重製再次登場，不過官方至今依然沒有給出明確的上市日期，僅表示遊戲預計於今年正式登場，想必是讓不少人等的相當痛苦吧？...']);
        News_list::create(["fname" => "cadin","imgName" => "2","title" => '《跑跑卡丁車》世界爭霸賽國家代表決賽 4名國家代表將赴韓爭奪世界冠軍',"content" => '遊戲橘子今年首度舉辦的《跑跑卡丁車》世界爭霸賽，於昨（4）日在世貿漫畫博覽會現場舉行2019《跑跑卡丁車》世界爭霸賽國家代表最終戰，來自各方實力強勁的代表隊在經過一連串精采絕倫的賽事後，最終由「爆哥」、「睏平」...']); 
    }
}


 
