import { ref, computed } from 'vue'

const currentLanguage = ref('TR')

const translations = {
  EN: {
    title: "Calling all DJs... something massive is on the way!",
    nowPlaying: "Now Playing",
    radioName: "DJ Evoo Radio",
    features: {
      1: {
        title: "A Global Stage for DJs",
        content: "Djevoo is a next-gen digital stage designed for DJs to showcase their talent, share their sets with the world, and connect directly with venues. No longer limited by location, DJs can expand their reach, grow their audience, and increase their chances of landing real gigs. Whether you're an emerging artist or an experienced performer, Djevoo offers a unique platform where you can express your style, gain visibility, and build your brand. Let your music travel beyond borders — Djevoo makes it possible."
      },
      2: {
        title: "Seamless Discovery for Music Lovers",
        content: "Djevoo gives music lovers instant access to fresh DJ sets from around the world. Whether you're into house, techno, deep, or minimal, the app makes it easy to discover new sounds and follow your favorite DJs. Dive into curated mixes, explore unique performances, and stay updated with the latest in electronic music culture. With Djevoo, you don't just listen — you explore, connect, and experience music in a more personal and engaging way. Great sound is always just a tap away."
      },
      3: {
        title: "A Smart Tool for Venues and Event Planners",
        content: "Djevoo helps bars, clubs, and event organizers easily discover and connect with DJs who fit their vibe. Browse DJ profiles, listen to past performances, and contact artists directly — all within the platform. Whether you're looking to book talent for a regular night or a special event, Djevoo streamlines the process and ensures you find the right sound for your space. Say goodbye to endless scouting. Say hello to a new era of booking DJs efficiently and professionally."
      },
      4: {
        title: "A Global Network for DJs",
        content: "Djevoo is more than a music-sharing platform — it's a thriving community of DJs from around the globe. Here, artists can follow each other, collaborate, exchange ideas, and support one another's journeys. Whether you're seeking inspiration, new opportunities, or creative partnerships, Djevoo gives you the space to grow and connect with like-minded talents. Build relationships, expand your network, and become part of a global scene where collaboration meets innovation."
      },
      5: {
        title: "A Streamlined Mobile Experience",
        content: "Designed for simplicity and speed, Djevoo's mobile interface allows users to navigate easily between DJ profiles, listen to new sets, and interact with content smoothly. Whether you're a listener searching for fresh music or a DJ managing your profile, everything is accessible with just a few taps. Notifications ensure you never miss a new set or live session. With Djevoo, high-quality music meets effortless access — whenever and wherever you are."
      },
      6: {
        title: "Real-Time Streaming and Engagement",
        content: "Djevoo empowers DJs to stream live sets and engage with their followers in real time. This feature turns your phone into a digital stage, where your energy and music reach listeners directly. Viewers can comment, react, and interact during live sessions, turning a simple set into a shared experience. Whether you're performing from your home studio or a packed club, Djevoo helps you build a loyal fan base and keep the vibe alive — even offstage."
      },
      7: {
        title: "Showcase Yourself Like a Pro",
        content: "Every DJ has a dedicated profile on Djevoo, featuring their bio, musical style, venue history, uploaded sets, and upcoming performances. It's your space to express who you are and what you sound like. This professional presentation helps you attract gigs, grow your brand, and stand out in a crowded music scene. Whether you're seeking bookings or just want to share your vibe with the world, Djevoo gives you the platform to do it all — on your terms."
      }
    }
  },
  TR: {
    title: "Tüm DJ'leri çağırıyoruz... büyük bir şey geliyor!",
    nowPlaying: "Şimdi Çalıyor",
    radioName: "DJ Evoo Radyo",
    features: {
      1: {
        title: "DJ'ler için Global Sahne",
        content: "Djevoo, DJ'lerin sadece müziklerini değil, kendilerini de dünyaya tanıtmaları için tasarlanmış yeni nesil bir dijital sahnedir. DJ'ler platforma setlerini yükleyerek müzikseverlerle doğrudan buluşabilir, kendilerini tanıtabilir ve kariyerlerini büyütebilir. Artık DJ'lik sadece lokal bir sahneyle sınırlı değil. Djevoo ile yeteneğini sınırların ötesine taşıyabilir, takipçi kazanabilir ve gerçek kulüp performanslarına ulaşma şansı yakalayabilirsin. DJ'lik kariyerini dijitalde büyütmek isteyen herkes için Djevoo, yeni bir başlangıç noktası."
      },
      2: {
        title: "Müzikseverler için Kolay Keşif",
        content: "Djevoo, elektronik müzik tutkunları için sınırsız keşif imkanı sunar. Kullanıcılar platformda, farklı tarzlarda çalan yerli ve yabancı DJ'lerin en güncel setlerine tek tıkla ulaşabilir. DJ'lerin profillerini inceleyebilir, geçmiş performanslarını dinleyebilir ve en yeni içeriklere kolayca erişebilir. Sadece müzik dinlemekle kalmazsın; aynı zamanda müzikteki yeni akımları keşfeder, favori DJ'lerini takip edebilir ve müzik zevkini genişletebilirsin. Djevoo, müzik dinlemeyi sosyal ve interaktif bir deneyime dönüştürür."
      },
      3: {
        title: "Mekanlar için DJ Erişimi",
        content: "Barlar, kulüpler ve etkinlik organizatörleri için doğru DJ'yi bulmak artık çok daha kolay. Djevoo, DJ'lerin geçmiş performanslarını, müzik tarzlarını ve set örneklerini inceleme imkanı sunar. Böylece mekanlar, etkinliklerine en uygun DJ'yi seçebilir, doğrudan iletişime geçebilir ve zaman kaybetmeden iş birliği kurabilir. Djevoo, etkinlik sektörünü dijitalleştirerek daha hızlı, daha etkili ve profesyonel bağlantıların kurulmasını sağlar. Mekanlar için DJ arama süreci artık sadece birkaç adım."
      },
      4: {
        title: "DJ'ler Arası Global Network",
        content: "Djevoo sadece bir içerik platformu değil, aynı zamanda bir DJ topluluğudur. DJ'ler birbirlerini takip edebilir, bağlantı kurabilir ve hatta birlikte projeler üretebilir. Bu sayede Djevoo, dijital bir sahne olmanın ötesinde küresel bir iş birliği alanı sunar. Yeni çıkan DJ'ler deneyimlilerden ilham alabilir, networkünü genişletebilir ve yeni sahnelere adım atabilir. Müzik dünyasında yalnız değil, bir topluluğun parçası olmak artık çok daha kolay. Djevoo, DJ'ler arası bağ kurmayı hızlandırır ve güçlendirir."
      },
      5: {
        title: "Kullanıcı Dostu Mobil Deneyim",
        content: "Djevoo, sade, hızlı ve sezgisel bir mobil uygulama deneyimi sunar. Hem DJ'ler hem dinleyiciler için tasarlanan arayüz sayesinde kullanıcılar diledikleri DJ'yi kolayca bulabilir, favori setlerini dinleyebilir ve uygulama içinde sorunsuz gezinir. Arayüz, müzikle etkileşimi ön plana çıkarırken, aynı zamanda kullanıcıya kişisel bir alan sunar. Bildirimler ile en son yüklenen setlerden haberdar ol, favori DJ'lerini anında takip et. Djevoo ile müzik her zaman seninle, sadece bir dokunuş kadar yakın."
      },
      6: {
        title: "Canlı Yayın ve Etkileşim",
        content: "Djevoo, DJ'lere canlı yayın yapma ve anlık etkileşim kurma olanağı sunar. Bu özellik sayesinde DJ'ler sadece içerik yüklemekle kalmaz, aynı zamanda takipçileriyle doğrudan bağlantı kurarak canlı performanslarını dijital sahnede sergileyebilir. Dinleyiciler ise yorum yapabilir, beğenebilir ve gerçek zamanlı etkileşimin keyfini çıkarabilir. Bu özellik DJ'lere sadece görünürlük kazandırmakla kalmaz, aynı zamanda sadık bir dinleyici kitlesi oluşturmalarına da yardımcı olur. Dijital sahnede gerçek zamanlı enerji Djevoo'da!"
      },
      7: {
        title: "Kendini Tanıtma Alanı",
        content: "Her DJ'in kendi profil sayfası; biyografisi, tarzı, çaldığı mekanlar, geçmiş setleri ve yaklaşan etkinlikleriyle doludur. Bu alan sayesinde DJ'ler kendilerini profesyonelce ifade edebilir, iş birliği ve teklif alma olasılıklarını artırabilir. DJ profilleri sadece müziği değil, kişiliği ve vizyonu da yansıtır. Djevoo, DJ'lere görünürlük, markalaşma ve kitle oluşturma fırsatını bir arada sunar. Sadece müzik dinletmek değil, müziğinle bir etki yaratmak istiyorsan, burası senin yerin."
      }
    }
  },
  HE: {
    title: "קוראים לכל הדיג'יים... משהו ענק בדרך!",
    nowPlaying: "מתנגן עכשיו",
    radioName: "רדיו DJ Evoo",
    features: {
      1: {
        title: "במה עולמית ל-DJs",
        content: "Djevoo היא במה דיגיטלית חדשנית שתוכננה במיוחד ל-DJs כדי להציג את הכישרון שלהם, לשתף את הסטים שלהם עם העולם וליצור קשר ישיר עם מקומות הופעה. לא מוגבלים עוד למיקום – DJs יכולים להרחיב את קהל המאזינים שלהם, לשפר את הנראות שלהם ולהגדיל את הסיכויים לקבל הזמנות אמיתיות. בין אם אתה אמן מתעורר או וותיק, Djevoo מציעה פלטפורמה ייחודית שבה תוכל לבטא את הסגנון שלך, לבנות את המותג שלך ולאפשר למוזיקה שלך לנדוד מעבר לגבולות – עם Djevoo זה אפשרי."
      },
      2: {
        title: "חווית גילוי שוטפת לאוהבי מוזיקה",
        content: "Djevoo מעניקה למאזינים גישה מידית לסטים חדשים של DJs מרחבי העולם. בין אם אתה אוהב האוס, טכנו, דיפ או מינימל, האפליקציה מאפשרת לגלות צלילים חדשים ולעקוב אחר ה-DJs האהובים עליך. צלול לתוך מיקסים מבוקרים, חפש הופעות ייחודיות, והישאר מעודכן בתרבות המוזיקה האלקטרונית העדכנית ביותר. עם Djevoo, אתה לא רק מאזין — אתה חוקר, מתחבר, וחווה מוזיקה בצורה אישית ומרתקת. צליל מצוין נמצא רק בלחיצה."
      },
      3: {
        title: "כלי חכם לבעלי עסקים ואירועים",
        content: "Djevoo מסייעת לברים, מועדונים ומארגני אירועים למצוא וליצור קשר עם DJs שמתאימים לאווירה שלהם. דפדוף בפרופילים, האזנה להופעות קודמות ויצירת קשר עם האמנים נעשה ישירות בפלטפורמה — בלי מאמץ נוסף. בין אם אתה מחפש DJ לאירוע קבוע או מיוחד, Djevoo מייעלת את התהליך ומבטיחה שתמצא את הצליל המתאים למקום שלך. שלום לחיפושים אינסופיים, שלום לעידן חדש של גיוס DJs בצורה יעילה ומקצועית."
      },
      4: {
        title: "רשת עולמית ל-DJs",
        content: "Djevoo היא לא רק פלטפורמה לשיתוף מוזיקה — היא קהילה חיונית של DJs מרחבי העולם. כאן האמנים יכולים לעקוב זה אחר זה, לשתף פעולה, להחליף רעיונות ולתמוך בדרכם. בין אם אתה מחפש השראה, הזדמנויות חדשות או שותפויות יצירתיות, Djevoo נותנת לך את המרחב לגדול ולהתחבר עם כישרונות אחרים. בנה קשרים, הרחיב את הרשת שלך והפוך לחלק מסצנה גלובלית שבה שיתוף פעולה פוגש חדשנות."
      },
      5: {
        title: "חוויית מובייל חלקה",
        content: "תוכננה לפשטות ולמהירות, הממשק הנייד של Djevoo מאפשר למשתמשים לעבור בקלות בין פרופילים, להאזין לסטים חדשים ולהתעדכן בצורה חלקה. אם אתה מאזין שמחפש מוזיקה חדשה או DJ שמנהל את הפרופיל שלו — הכל נגיש בכמה הקשות בלבד. התראות מבטיחות שלא תפספס סט חדש או שידור חי. עם Djevoo, מוזיקה איכותית פוגשת גישה נוחה — בכל זמן ובכל מקום."
      },
      6: {
        title: "שידור ותגובות בזמן אמת",
        content: "Djevoo מאפשרת ל-DJs לשדר סטים חיים וליצור אינטראקציה בזמן אמת עם העוקבים שלהם. תכונה זו הופכת את המכשיר שלך לבמה דיגיטלית, שבה האנרגיה והמוזיקה שלך מגיעות ישירות למאזינים. הצופים יכולים להגיב, לעשות לייק ולהתפאעל במהלך השידור החי, והופכים כל סט לחוויה משותפת. בין אם אתה מופיע מהסטודיו ביתי או ממועדון מלא — Djevoo עוזרת לך לבנות קהל מאהב ולהמשיך את הוויב — גם מחוץ לבמה."
      },
      7: {
        title: "הצג את עצמך כמו מקצוען",
        content: "לכל DJ יש פרופיל ייעודי ב-Djevoo, הכולל ביוגרפיה, סגנון מוזיקלי, היסטוריית הופעות, סטים שהועלו ואירועים קרובים. זוהי הסביבה שלך להציג מי אתה ומה הסאונד שלך. הצגה מקצועית זו מסייעת לך למשוך הופעות, לגדול את המותג שלך ולהתבלט בזירת המוזיקה. בין אם אתה מחפש הופעות או רוצה פשוט לחלוק את הוייב שלך עם העולם — Djevoo נותנת לך את הפלטפורמה לעשות הכל — בתנאים שלך."
      }
    }
  },
  RU: {
    title: "Внимание всем диджеям... грядет что-то грандиозное!",
    nowPlaying: "Сейчас играет",
    radioName: "Радио DJ Evoo",
    features: {
      1: {
        title: "Глобальная сцена для диджеев",
        content: "Djevoo — это платформа нового поколения, созданная для диджеев, чтобы демонстрировать свой талант, делиться сетами с миром и напрямую связываться с площадками. Больше никаких географических ограничений — диджеи могут расширять свою аудиторию, увеличивать видимость и повышать шансы получить реальные выступления. Независимо от того, начинающий вы артист или опытный исполнитель, Djevoo предлагает уникальную площадку для самовыражения, продвижения и построения бренда. Пусть ваша музыка пересекает границы — с Djevoo это возможно."
      },
      2: {
        title: "Простой поиск для меломанов",
        content: "Djevoo предоставляет мгновенный доступ к свежим сетам диджеев со всего мира. Любите ли вы хаус, техно, дип или минимал — в приложении легко открыть новые звуки и следить за любимыми артистами. Погружайтесь в курируемые миксы, исследуйте уникальные выступления и будьте в курсе последних событий электронной музыкальной культуры. С Djevoo вы не просто слушаете — вы исследуете, подключаетесь и переживаете музыку в ещё более личном и вовлекающем формате. Отличный звук — всего в одном касании."
      },
      3: {
        title: "Умный инструмент для клубов и организаторов",
        content: "Djevoo помогает барам, клубам и организаторам мероприятий легко находить и взаимодействовать с диджеями, соответствующими формату. Просматривайте профили, слушайте прошлые выступления и связывайтесь с артистами прямо через платформу. Ищете ли вы артиста для регулярного вечера или особого события, Djevoo упрощает процесс и гарантирует, что вы найдете подходящий саунд для вашего заведения. Попрощайтесь с бесконечными поисками и здравствуйте новой эре профессионального взаимодействия с диджеями."
      },
      4: {
        title: "Глобальная сеть диджеев",
        content: "Djevoo — это не просто музыкальная платформа, это живое сообщество диджеев со всего мира. Здесь артисты могут следить друг за другом, сотрудничать, обмениваться идеями и поддерживать друг друга. Независимо от того, ищете ли вы вдохновение, новые возможности или творческие партнёрства, Djevoo предоставляет пространство для роста и взаимодействия с единомышленниками. Стройте связи, расширяйте свою сеть и становитесь частью глобальной сцены, где сотрудничество сочетается с инновациями."
      },
      5: {
        title: "Удобный мобильный опыт",
        content: "Разработанный для простоты и скорости, мобильный интерфейс Djevoo позволяет пользователям легко переключаться между профилями диджеев, слушать новые сеты и взаимодействовать с контентом без задержек. Если вы слушатель, ищущий свежую музыку, или DJ, управляющий своим профилем — всё доступно в несколько тапов. Уведомления помогут не пропустить новые релизы или живые трансляции. С Djevoo качественная музыка сочетается с простым и удобным доступом — в любое время и в любом месте."
      },
      6: {
        title: "Прямые трансляции и взаимодействие",
        content: "Djevoo дает диджеям возможность проводить прямые эфиры и сразу взаимодействовать с поклонниками. Эта функция превращает ваш телефон в цифровую сцену, где ваша энергия и музыка доходят до слушателей напрямую. Зрители могут комментировать, реагировать и участвовать во время эфира, превращая обычный сет в живой опыт. Независимо от того, выступаете ли вы из домашней студии или полного клуба — Djevoo поможет вам создать лояльную аудиторию и поддерживать энергию — даже вне сцены."
      },
      7: {
        title: "Представьте себя как профи",
        content: "У каждого DJ есть личный профиль на Djevoo, включающий биографию, музыкальный стиль, историю выступлений, загруженные сеты и предстоящие события. Это ваше пространство, чтобы рассказать, кто вы и как звучит ваша музыка. Профессиональное представление помогает привлечь выступления, развить бренд и выделиться на насыщенной музыкальной сцене. Хотите найти выступления или просто поделиться своим вайбом с миром — Djevoo предоставляет платформу для всего этого — на ваших условиях."
      }
    }
  }
}

export function useLanguage() {
  const setLanguage = (lang) => {
    currentLanguage.value = lang
    localStorage.setItem('language', lang)
  }

  const t = (key) => {
    const keys = key.split('.')
    let result = translations[currentLanguage.value]
    
    for (const k of keys) {
      if (result && typeof result === 'object') {
        result = result[k]
      } else {
        return key // Return original key if path not found
      }
    }
    
    return result || key
  }

  const detectCountryAndSetLanguage = async () => {
    try {
      let countryCode = null
      
      // Birincil API: ipapi.co
      try {
        const response = await fetch('https://ipapi.co/json/')
        const data = await response.json()
        countryCode = data.country_code
      } catch (error) {
        console.log('ipapi.co failed, trying backup API')
      }
      
      // Fallback API: ip-api.com
      if (!countryCode) {
        const response = await fetch('http://ip-api.com/json/')
        const data = await response.json()
        countryCode = data.countryCode
      }
      
      // Ülke kodlarına göre dil mapping
      const countryToLanguage = {
        'TR': 'TR', // Türkiye
        'IL': 'HE', // İsrail  
        'RU': 'RU', // Rusya
        'BY': 'RU', // Belarus
        'KZ': 'RU', // Kazakistan
        'UA': 'RU', // Ukrayna (Rusça konuşan bölgeler)
        // Diğer tüm ülkeler İngilizce
      }
      
      const detectedLanguage = countryToLanguage[countryCode] || 'EN'
      
      // localStorage'da kaydedilmiş dil varsa onu kullan, yoksa otomatik tespit et
      const savedLang = localStorage.getItem('language')
      if (savedLang && translations[savedLang]) {
        currentLanguage.value = savedLang
      } else {
        currentLanguage.value = detectedLanguage
        localStorage.setItem('language', detectedLanguage)
      }
      
      console.log(`Country detected: ${countryCode}, Language set to: ${currentLanguage.value}`)
      
    } catch (error) {
      console.error('IP detection failed, using default language:', error)
      // Hata durumunda varsayılan dil mantığı
      const savedLang = localStorage.getItem('language')
      if (savedLang && translations[savedLang]) {
        currentLanguage.value = savedLang
      } else {
        currentLanguage.value = 'EN' // Varsayılan İngilizce
      }
    }
  }

  const initLanguage = () => {
    // Eski fonksiyon backward compatibility için
    detectCountryAndSetLanguage()
  }

  return {
    currentLanguage: computed(() => currentLanguage.value),
    setLanguage,
    t,
    initLanguage
  }
}