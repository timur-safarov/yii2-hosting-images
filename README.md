<p align="center">
    <h1 align="center">Yii 2 image hosting</h1>
    <br>
</p>


# Поставить права на папку с картинками
```sudo chmod -R 777 frontend/web/images/```
```sudo chown -R www-data:www-data frontend/web/images/```

- Или просто удалить папку если она пустая, а при записи файлов
- сервер сам установит её нужно пользователя

# API
1). Получение всех картинок
- GET /api-images

2). Получение конкретной картинки
- GET /api-images/{id}