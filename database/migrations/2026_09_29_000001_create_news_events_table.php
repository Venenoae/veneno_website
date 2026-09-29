<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('news_events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('title_ar')->nullable();
            $table->string('slug')->unique();
            $table->string('type')->default('event'); // 'event' or 'news'
            $table->string('category')->default('Event'); // 'Challenge', 'Exhibition', 'Launch', 'Detailing', 'Announcement'
            $table->dateTime('event_date')->nullable();
            $table->string('event_time')->nullable();
            $table->string('location')->nullable();
            $table->string('location_ar')->nullable();
            $table->text('summary')->nullable();
            $table->text('summary_ar')->nullable();
            $table->longText('content')->nullable();
            $table->longText('content_ar')->nullable();
            $table->string('image_url')->nullable();
            $table->string('badge')->nullable(); // 'Concluded', 'Upcoming', 'Exclusive', 'New Release'
            $table->string('badge_ar')->nullable();
            $table->string('status')->default('published'); // 'published', 'draft', 'archived'
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_past')->default(false);
            $table->string('prize_podium')->nullable();
            $table->json('gallery_images')->nullable();
            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index(['type', 'status']);
            $table->index(['is_featured', 'event_date']);
        });

        // Seed initial historic & previous events and news articles
        DB::table('news_events')->insert([
            [
                'title' => 'Veneno Hammer Challenge — The Final',
                'title_ar' => 'تحدي مطرقة فينينو — النهائي الكبير',
                'slug' => 'veneno-hammer-challenge-the-final',
                'type' => 'event',
                'category' => 'Strength & Automotive Challenge',
                'event_date' => '2026-09-12 17:00:00',
                'event_time' => '5:00 PM – 10:00 PM',
                'location' => 'Veneno Auto Care Center, Musaffah M37, Abu Dhabi',
                'location_ar' => 'مركز فينينو للعناية بالسيارات، مصفح M37، أبوظبي',
                'summary' => 'The legendary Veneno Hammer Challenge climaxed in Abu Dhabi with top UAE athlete participants competing for the AED 15,000 1st place cash prize, accompanied by an electrifying live spectator raffle draw.',
                'summary_ar' => 'اختتم تحدي مطرقة فينينو الأسطوري في أبوظبي بمنافسة نخبة رياضيي الإمارات على الجائزة النقدية الكبرى بقيمة 15,000 درهم للمركز الأول، وسط سحب جماهيري حماسي للحضور.',
                'content' => 'The Veneno Hammer Challenge Final took place on Saturday, 12 September 2026, at the state-of-the-art Veneno Auto Care Center facility in Musaffah M37, Abu Dhabi. Top athletes from across the Emirates demonstrated supreme physical power, precision, and endurance. Over 500 spectators and automotive enthusiasts witnessed the showdown, enjoying exclusive showroom tours, VIP vehicle care demonstrations, and an exhilarating live audience raffle with official certificate passes.',
                'content_ar' => 'أقيمت الجولة النهائية لـ تحدي مطرقة فينينو يوم السبت 12 سبتمبر 2026 في مقر مركز فينينو المتطور في مصفح M37 بأبوظبي. تنافس نخبة الرياضيين من كافة إمارات الدولة مقدمين أعلى مستويات القوة البدنية والدقة والتحمل. شهدت الفعالية حضور أكثر من 500 زائر وعشاق السيارات الخارقة واستمتعوا بجولات حصرية وعروض لحماية السيارات وسحب مباشر لجوائز الجمهور.',
                'image_url' => '/images/hammer/Hammer1.jpeg',
                'badge' => 'Concluded Event',
                'badge_ar' => 'فعالية مكتملة',
                'status' => 'published',
                'is_featured' => true,
                'is_past' => true,
                'prize_podium' => '1st Place: AED 15,000 Cash Prize + Audience Raffle Draw',
                'gallery_images' => json_encode([
                    '/images/hammer/Hammer1.jpeg',
                    '/images/hammer/Hammer3.jpeg',
                    '/images/hammer/Hammer2.jpeg',
                    '/images/hammer/Hammer4.jpeg',
                ]),
                'views_count' => 1250,
                'created_by' => null,
                'created_at' => '2026-09-12 10:00:00',
                'updated_at' => now(),
            ],
            [
                'title' => 'ADIHEX 2026 — Official Veneno Exhibition & VIP Lounge',
                'title_ar' => 'معرض الصيد والفروسية الدولي أبوظبي 2026 — جناح فينينو الرسمي',
                'slug' => 'adihex-2026-official-veneno-exhibition',
                'type' => 'event',
                'category' => 'International Exhibition',
                'event_date' => '2026-09-06 10:00:00',
                'event_time' => '10:00 AM – 10:00 PM',
                'location' => 'ADNEC Centre Abu Dhabi, Hall 4',
                'location_ar' => 'مركز أدنيك أبوظبي، القاعة 4',
                'summary' => 'Veneno Auto Care Center concluded a groundbreaking multi-day presence at ADIHEX 2026, welcoming thousands of visitors with our gamified luxury spin wheel, exclusive show packages, and VIP deposits.',
                'summary_ar' => 'اختتم مركز فينينو للعناية بالسيارات مشاركة رائدة واستثنائية في معرض أبوظبي الدولي للصيد والفروسية 2026، مستقبلاً آلاف الزوار بعروض حصرية وتجارب تفاعلية وباقات العناية الفاخرة.',
                'content' => 'At ADNEC Hall 4, Veneno Auto Care showcased the pinnacle of automotive craftsmanship, including bespoke 3M Paint Protection Film (PPF) installations, hydrophobic GYEON Quartz nano-ceramic coatings, and high-heat ceramic window tints engineered for Gulf climates. Our digital kiosk activation engaged thousands of patrons across the UAE.',
                'content_ar' => 'في القاعة 4 بمركز أدنيك، استعرض مركز فينينو قمة الحرفية في حماية السيارات الفارهة، بما في ذلك أفلام حماية الطلاء المعتمدة من 3M، وطلاء النانو سيراميك الكاره للماء من GYEON، وعوازل النوافذ الحرارية المخصصة لأجواء الخليج.',
                'image_url' => '/images/main-branch.webp',
                'badge' => 'Concluded Showcase',
                'badge_ar' => 'معرض مكتمل',
                'status' => 'published',
                'is_featured' => false,
                'is_past' => true,
                'prize_podium' => 'Exclusive Exhibition Vouchers & 500 AED VIP Booking Passes',
                'gallery_images' => json_encode([
                    '/images/main-branch.webp',
                ]),
                'views_count' => 3420,
                'created_by' => null,
                'created_at' => '2026-09-06 09:00:00',
                'updated_at' => now(),
            ],
            [
                'title' => 'Veneno Ceramic Armor & Matte Stealth PPF Defense Launch',
                'title_ar' => 'تدشين درع السيراميك وحماية المات الشبحية فائقة التحمل 2026',
                'slug' => 'veneno-ceramic-armor-matte-stealth-ppf-launch',
                'type' => 'news',
                'category' => 'Product Launch & Innovation',
                'event_date' => '2026-09-18 09:00:00',
                'event_time' => '09:00 AM',
                'location' => 'Veneno Studios Abu Dhabi',
                'location_ar' => 'استوديوهات فينينو أبوظبي',
                'summary' => 'Introducing next-generation self-healing Paint Protection Film with ultra-hydrophobic ceramic infusion, engineered specifically to withstand extreme UAE desert highway sand abrasion.',
                'summary_ar' => 'نقدم الجيل القادم من أفلام حماية الطلاء ذاتية الشفاء المعززة بالسيراميك فائق الكراهية للماء، والمصممة خصيصاً لمقاومة رمال وحرارة الطرق السريعة في الإمارات.',
                'content' => 'Veneno Auto Care Center announces the official deployment of our upgraded 2026 Ceramic Armor and Stealth Satin PPF film technologies. Formulated in partnership with tier-1 international material laboratories, this system guarantees 10-year warranty defense against gravel chips, UV yellowing, swirl marks, and environmental contamination.',
                'content_ar' => 'يعلن مركز فينينو للعناية بالسيارات عن التدشين الرسمي لتقنيات أفلام الحماية الساتان المات ودرع السيراميك المطور لعام 2026. تضمن هذه المنظومة حماية متكاملة لمدة 10 سنوات ضد الحصى وتغير اللون والأشعة فوق البنفسجية والعوامل الجوية.',
                'image_url' => '/images/services/ppf/IMG_5968.JPG',
                'badge' => 'Latest News',
                'badge_ar' => 'أحدث الأخبار',
                'status' => 'published',
                'is_featured' => false,
                'is_past' => false,
                'prize_podium' => null,
                'gallery_images' => json_encode([
                    '/images/services/ppf/IMG_5968.JPG',
                ]),
                'views_count' => 890,
                'created_by' => null,
                'created_at' => '2026-09-18 09:00:00',
                'updated_at' => now(),
            ],
            [
                'title' => 'VIP Supercar Detailing Experience & Yas Track Day Prep',
                'title_ar' => 'تجربة العناية بالسيارات الخارقة والتحضير لحلبة مرسى ياس',
                'slug' => 'vip-supercar-detailing-yas-track-day-prep',
                'type' => 'event',
                'category' => 'Track & Performance',
                'event_date' => '2026-10-25 15:00:00',
                'event_time' => '3:00 PM – 8:00 PM',
                'location' => 'Veneno Auto Care Center, Musaffah M37 & Yas Marina Circuit',
                'location_ar' => 'مركز فينينو مصفح M37 وحلبة مرسى ياس، أبوظبي',
                'summary' => 'An exclusive invitation-only track day preparation workshop for supercar and hypercar owners, featuring aerodynamic glass coatings, brake caliper heat protection, and high-velocity film testing.',
                'summary_ar' => 'ورشة عمل وتجهيز حصري بالدعوات لأصحاب السيارات الخارقة والرياضية للتحضير لجولات حلبة ياس، مع طلاء الزجاج الإيروديناميكي وحماية كليبرات الفرامل من الحرارة.',
                'content' => 'Join the Veneno Engineering Team for an intensive performance-detailing day. Learn track-side vehicle protection techniques, tire & wheel ceramic armor preservation under extreme track temperatures, and real-time paint inspection diagnostics.',
                'content_ar' => 'انضم إلى الفريق الهندسي لمركز فينينو في يوم مخصص لتجهيز وحماية السيارات الرياضية قبل دخول الحلبة. تعرف على تقنيات حماية الطلاء والجنوط في درجات الحرارة القصوى.',
                'image_url' => '/images/gallery/IMG_5866.JPG',
                'badge' => 'Upcoming Event',
                'badge_ar' => 'فعالية قادمة',
                'status' => 'published',
                'is_featured' => false,
                'is_past' => false,
                'prize_podium' => 'VIP Track Pack & GYEON Detailing Kit',
                'gallery_images' => json_encode([
                    '/images/gallery/IMG_5866.JPG',
                ]),
                'views_count' => 450,
                'created_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news_events');
    }
};
