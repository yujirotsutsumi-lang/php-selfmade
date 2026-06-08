<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Faker\Factory as Faker;

class ReflectionTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('ja_JP');
        
        // テストデータを紐付けるユーザーID（仮に1とします。環境に合わせて変更してください）
        $userId = 1;

        // 2026年4月1日〜2026年4月30日までの期間を設定
        $startDate = Carbon::create(2026, 4, 1, 0, 0, 0);
        $endDate = Carbon::create(2026, 4, 30, 23, 59, 59);

        // テスト用の付箋コメント用の素材
        $sampleContents = [
            '1' => ['タスクAを完了できた！', '早起きして進捗を生めた', 'チームメンバーに褒められた', '実装がスムーズにいった'],
            '2' => ['エラーの解決に時間がかかりすぎた', '設計を少し見直す必要がありそう', 'もっと早めに相談すればよかった'],
            '3' => ['明日までに設計書を書き上げる', '午前中に重いタスクを終わらせる', 'リファクタリングをやる']
        ];

        // 4月の1日ずつループしてデータを作る
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            
            // 💡 10段階のヒートマップを再現するため、1日の付箋枚数をランダムに決定
            // 0枚（グレー）から最大30枚（レベル9カンスト）までバラつかせる
            // 15%の確率で「活動なし（0枚）」の日を作る
            if (rand(1, 100) <= 15) {
                continue;
            }

            // 1日に作成する付箋の枚数（1枚〜28枚のランダム）
            $noteCount = rand(1, 28);

            for ($i = 0; $i < $noteCount; $i++) {
                // カテゴリーIDをランダムに決定（0:未仕分け, 1:成功, 2:学び, 3:明日やる）
                $categoryId = rand(0, 3);
                
                // カテゴリーに応じたテキスト、または汎用テキストを生成
                $content = $sampleContents[$categoryId] ?? ['日常のメモ・振り返りタスク', '気になる技術の調査'];
                $finalContent = $faker->randomElement($content) . "\n" . $faker->realText(20);

                DB::table('notes')->insert([
                    'user_id' => $userId,
                    'category_id' => $categoryId,
                    'content' => $finalContent,
                    'is_starred' => $faker->boolean(10), // 10%の確率でスター付き
                    'created_at' => $date->copy()->hour(rand(9, 21))->minute(rand(0, 59)), // 9時〜21時の間でランダム
                    'updated_at' => $date,
                ]);
            }
        }

        $this->command->info('2026年4月のテストデータの生成が完了しました！');
    }
}