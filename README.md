```
composer i

php artisan key:generate

composer run dev

php artisan migrate:fresh --seed
(xóa DB → migrate lại → seed dữ liệu mẫu).

php artisan jwt:secret
```

1. tasks(): HasMany

Đây là quan hệ 1-nhiều (One to Many).

Nghĩa là một bản ghi của model hiện tại có thể có nhiều Task.

Ví dụ:

Nếu model hiện tại là Project, thì 1 Project có nhiều Task.

Project::find(1)->tasks sẽ trả về tất cả task thuộc project đó.

2. user(): BelongsTo

Đây là quan hệ ngược lại (nhiều-1).

Nghĩa là một bản ghi của model hiện tại thuộc về 1 User.

Ví dụ:

Nếu model hiện tại là Task, thì 1 Task chỉ thuộc về 1 User.

Task::find(1)->user sẽ trả về user mà task đó thuộc về.
