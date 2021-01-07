<?php

use Illuminate\Database\Seeder;
use App\Game_list;
class Game_list_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Game_list::truncate();
        Game_list::create(["inx" => "0" ,"platform" => "Online", "name" => "魔獸世界：決戰艾澤拉斯" , "imgsrc" => "wow.png", "imgsize" => "120x120"]); 
        Game_list::create(["inx" => "1" ,"platform" => "PC",     "name" => "帝國：全軍破敵" ,        "imgsrc" => "etw.jpg", "imgsize" => "100x130"]); 
        Game_list::create(["inx" => "2" ,"platform" => "PC",    "name" => "刺客教條 2" ,             "imgsrc" => "ac2.jpg", "imgsize" => "100x130"]); 
        Game_list::create(["inx" => "3" ,"platform" => "PC",    "name" => "全軍破敵：三國" ,         "imgsrc" => "ttw.jpg", "imgsize" => "100x130"]); 
        Game_list::create(["inx" => "4" ,"platform" => "PS4",    "name" => "惡靈古堡 2 重製版" ,      "imgsrc" => "bio2.png", "imgsize" => "100x130"]); 
        Game_list::create(["inx" => "5" ,"platform" => "Online", "name" => "暗黑破壞神 3：奪魂之鐮" , "imgsrc" => "d3.png", "imgsize" => "120x120"]); 
        Game_list::create(["inx" => "6" ,"platform" => "PS4",    "name" => "惡魔獵人 5" ,            "imgsrc" => "dmc5.jpg", "imgsize" => "100x130"]); 
        Game_list::create(["inx" => "7" ,"platform" => "PS4",    "name" => "漫威蜘蛛人" ,            "imgsrc" => "spm.png",  "imgsize" => "100x130"]); 
        Game_list::create(["inx" => "8" ,"platform" => "NS",    "name" => "勇者鬥惡龍 XI S 尋覓逝去的時光 – Definitive Edition" ,"imgsrc" => "dq.png", "imgsize" => "100x130"]); 
        Game_list::create(["inx" => "9","platform" => "PS4",    "name" => "魔物獵人 世界" ,         "imgsrc" => "mons.png","imgsize" => "100x130" ]); 
        Game_list::create(["inx" => "10","platform" => "PC",    "name" => "巫師 3：狂獵" ,          "imgsrc" => "witch.jpg","imgsize" => "100x130" ]); 
        Game_list::create(["inx" => "11","platform" => "NS",    "name" => "寶可夢 劍" ,             "imgsrc" => "poke.png","imgsize" => "100x130" ]); 
        Game_list::create(["inx" => "12","platform" => "NS",    "name" => "薩爾達傳說 曠野之息" ,    "imgsrc" => "zelda.png","imgsize" => "100x130" ]); 
        Game_list::create(["inx" => "13","platform" => "Online", "name" => "佩里亞編年史" ,          "imgsrc" => "peria.jpg","imgsize" => "120x120" ]); 
        Game_list::create(["inx" => "14" ,"platform" => "NS",     "name" => "哆啦 A 夢 牧場物語" ,    "imgsrc" => "dora.png", "imgsize" => "100x130"]);
 

    }
}
