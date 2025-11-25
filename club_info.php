<?php
session_start();
// Проверяем, что club_id передан в URL
$club_id = $_GET['id'] ?? null;
if (!$club_id || !is_numeric($club_id)) {
    header("Location: clubs.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Информация о клубе - CyberBook</title>

    <!-- Подключаем основные стили -->
    <link rel="stylesheet" href="css/style.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Стили для этой страницы -->
    <style>
        /* Отступ сверху, чтобы контент не прятался под фиксированной шапкой */
        .club-info-container {
            margin-top: 80px;
            padding: 2rem;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Карточка клуба */
        .club-card {
            background: white;
            border-radius: 12px;
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        /* Шапка клуба */
        .club-header {
            display: flex;
            align-items: center;
            gap: 2rem;
            padding: 2rem;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
        }

        .club-image {
            width: 120px;
            height: 120px;
            border-radius: 12px;
            object-fit: cover;
            border: 3px solid rgba(255, 255, 255, 0.3);
        }

        .club-info {
            flex: 1;
        }

        .club-title {
            font-size: 1.8rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            color: white;
        }

        .club-rating {
            font-size: 1.2rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: white;
        }

        .club-meta {
            display: flex;
            gap: 2rem;
            margin-top: 1rem;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.8);
        }

        /* Описание */
        .club-description {
            padding: 2rem;
            line-height: 1.6;
        }

        /* Детали */
        .club-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            padding: 1rem 2rem;
            background: #f8fafc;
        }

        @media (max-width: 768px) {
            .club-header {
                flex-direction: column;
                text-align: center;
            }
            .club-meta {
                flex-direction: column;
                gap: 0.5rem;
            }
            .club-details {
                grid-template-columns: 1fr;
            }
        }

        /* Секция с характеристиками ПК */
        .pc-specs-section {
            margin-top: 2rem;
            padding: 2rem;
            background: white;
            border-radius: 12px;
            box-shadow: var(--shadow);
        }

        .pc-specs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .spec-card {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 1rem;
            background: #f8fafc;
        }

        .spec-card h4 {
            margin-top: 0;
            font-size: 1rem;
            font-weight: 600;
            color: var(--dark);
        }

        /* Фото */
        .photos-section {
            margin-top: 2rem;
            padding: 2rem;
            background: white;
            border-radius: 12px;
            box-shadow: var(--shadow);
        }

        .photos-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
        }

        .photo-item {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        /* Кнопка бронирования */
        .btn-book-now {
            display: inline-block;
            margin-top: 2rem;
            padding: 0.875rem 2rem;
            border-radius: 12px;
            background: var(--primary);
            color: white;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-book-now:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        /* Загрузка и ошибки */
        .loading, .error {
            text-align: center;
            padding: 4rem;
            font-size: 1.2rem;
        }

        .error {
            color: red;
        }
    </style>
</head>
<body>
    <!-- Шапка (Header) -->
    <header class="header" id="header">
        <div class="container">
            <nav class="nav">
                <a href="index.php" class="logo">CyberBook</a>
                <div class="nav-links">
                    <a href="clubs.php">Клубы</a>
                    <a href="index.php#how-it-works">Как это работает</a>
                    <a href="index.php#features">Преимущества</a>
                    <?php if (isset($_SESSION['logged_in'])): ?>
                        <a href="profile.php" class="btn-outline">👤 <?php echo $_SESSION['user_name']; ?></a>
                    <?php else: ?>
                        <a href="login.php" class="btn-outline">Войти</a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </header>

    <!-- Основное содержимое страницы -->
    <main>
        <div class="club-info-container">
            <div id="club-info-content">
                <div class="loading">Загрузка информации о клубе...</div>
            </div>
        </div>
    </main>

    <!-- Футер (Footer) -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h4>CyberBook</h4>
                    <p>Передовая система бронирования для киберспортивных клубов.</p>
                </div>
                <div class="footer-section">
                    <h4>Компания</h4>
                    <ul class="footer-links">
                        <li><a href="about.php">О нас</a></li>
                        <li><a href="business.php">Для бизнеса</a></li>
                        <li><a href="contacts.php">Контакты</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Поддержка</h4>
                    <ul class="footer-links">
                        <li><a href="help.php">Помощь</a></li>
                        <li><a href="faq.php">FAQ</a></li>
                        <li><a href="rules.php">Правила</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Контакты</h4>
                    <p>support@cyberbook.ru<br>+7 (495) 123-45-67<br>Москва, ул. Геймерская, 15</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 CyberBook. Все права защищены. Сделано с любовью для геймеров</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript для загрузки данных -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const clubId = new URLSearchParams(window.location.search).get('id');
            const contentDiv = document.getElementById('club-info-content');

            if (!clubId) {
                contentDiv.innerHTML = '<div class="error">ID клуба не указан.</div>';
                return;
            }

            // Функция для загрузки информации о клубе
            async function loadClubInfo(id) {
                try {
                    const response = await fetch(`api/club_details.php?id=${encodeURIComponent(id)}`);
                    if (!response.ok) {
                        throw new Error(`Ошибка HTTP: ${response.status}`);
                    }
                    const clubData = await response.json();
                    renderClubInfo(clubData);
                } catch (error) {
                    console.error('Ошибка загрузки информации о клубе:', error);
                    contentDiv.innerHTML = `<div class="error">Не удалось загрузить информацию о клубе: ${error.message}</div>`;
                }
            }

            // Функция для отображения информации о клубе
            function renderClubInfo(data) {
                // Преобразуем rating в число
                const rating = parseFloat(data.rating) || 0;
                const hourlyRate = parseFloat(data.hourly_rate) || 0;

                // Формируем HTML
                let photosHTML = '';
                if (data.photos && data.photos.length > 0) {
                    photosHTML = `
                        <div class="photos-section">
                            <h3>Фото из клуба</h3>
                            <div class="photos-container">
                                ${data.photos.map(photo => `<img src="${photo.photo_url}" alt="Фото клуба" class="photo-item">`).join('')}
                            </div>
                        </div>
                    `;
                } else {
                    photosHTML = `
                        <div class="photos-section">
                            <h3>Фото из клуба</h3>
                            <p>Фотографии пока не загружены.</p>
                        </div>
                    `;
                }

                let pcSpecsHTML = '';
                if (data.pc_specs && data.pc_specs.length > 0) {
                    // Группируем характеристики по зонам
                    const specsByZone = {};
                    data.pc_specs.forEach(spec => {
                        if (!specsByZone[spec.zone]) {
                            specsByZone[spec.zone] = [];
                        }
                        specsByZone[spec.zone].push(spec);
                    });

                    pcSpecsHTML = `
                        <div class="pc-specs-section">
                            <h3>Характеристики ПК</h3>
                            <div class="pc-specs-grid">
                                ${Object.entries(specsByZone).map(([zone, specs]) => `
                                    <div class="spec-card">
                                        <h4>${zone}</h4>
                                        <p><strong>CPU:</strong> ${specs[0].cpu}</p>
                                        <p><strong>GPU:</strong> ${specs[0].gpu}</p>
                                        <p><strong>RAM:</strong> ${specs[0].ram}</p>
                                        <p><strong>Monitor:</strong> ${specs[0].monitor}</p>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    `;
                }

                let reviewsHTML = '';
                if (data.reviews && data.reviews.length > 0) {
                    reviewsHTML = `
                        <div class="reviews-section">
                            <h3>Отзывы (${data.reviews.length})</h3>
                            ${data.reviews.map(review => `
                                <div class="review-item">
                                    <div class="review-meta">
                                        <span>${review.first_name} ${review.last_name}</span>
                                        <span>⭐ ${review.rating}</span>
                                    </div>
                                    <p>${review.comment || 'Без комментария'}</p>
                                    <small>${new Date(review.created_at).toLocaleDateString('ru-RU')}</small>
                                </div>
                            `).join('')}
                        </div>
                    `;
                }

                contentDiv.innerHTML = `
                    <div class="club-card">
                        <div class="club-header">
                            <!-- Заглушка для изображения, так как мы не используем image_url -->
                            <div class="club-image" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem;">🎮</div>
                            <div class="club-info">
                                <h1 class="club-title">${data.name}</h1>
                                <div class="club-rating">⭐ ${rating.toFixed(1)}</div>
                                <div class="club-meta">
                                    <span>📍 ${data.address}</span>
                                    <span>🕒 ${data.is_24h ? 'Круглосуточно' : `с ${data.open_time} до ${data.close_time}`}</span>
                                </div>
                            </div>
                        </div>

                        <div class="club-description">
                            <h3>Описание</h3>
                            <p>${data.description}</p>
                        </div>

                        <div class="club-details">
                            <div>
                                <div><strong>Адрес:</strong> ${data.address}</div>
                                <div><strong>Город:</strong> ${data.city}</div>
                                <div><strong>Стоимость:</strong> ${hourlyRate.toFixed(2)} ₽ / час</div>
                            </div>
                            <div>
                                <div><strong>Режим работы:</strong> ${data.is_24h ? 'Круглосуточно' : `с ${data.open_time} до ${data.close_time}`}</div>
                                <div><strong>Телефон:</strong> Не указан</div>
                                <div><strong>Email:</strong> Не указан</div>
                            </div>
                        </div>

                        ${photosHTML}

                        ${pcSpecsHTML}

                        ${reviewsHTML}

                        <div style="text-align: center; padding: 2rem;">
                            <a href="booking.php?club_id=${data.id}" class="btn-book-now">Забронировать место</a>
                        </div>
                    </div>
                `;
            }

            // Загружаем информацию при загрузке страницы
            loadClubInfo(clubId);
        });
    </script>
</body>
</html>