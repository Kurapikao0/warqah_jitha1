#!/usr/bin/env python3
"""
Laravel 12 REST API Architecture Generator

Creates:
laravel_controllers_architecture/
and compresses it to:
laravel_controllers_architecture.zip

The generated structure follows:
- Laravel 12
- Sanctum
- Admin/User API separation
- Controllers
- Requests
- Resources
- Policies
- Services
- Routes

Run:
python generate_laravel_architecture.py
"""

from pathlib import Path
import zipfile

ROOT = Path("laravel_controllers_architecture")

FILES = {
    "routes/api.php": """<?php
// Generated API route file.
// Add Laravel Sanctum protected admin and user route groups here.
""",

    "README.md": """# Laravel 12 REST API Architecture

Generated structure for:
- Admin Guard
- User Guard
- Sanctum Authentication
- REST Controllers
- Services
- Form Requests
- API Resources
- Policies

Copy folders into your Laravel project root.
""",

}

CONTROLLERS = [
    "Admin/ProductController.php",
    "Admin/CategoryController.php",
    "Admin/ProductTypeController.php",
    "Admin/AttributeController.php",
    "Admin/RawMaterialController.php",
    "Admin/OrderController.php",
    "Admin/CustomDesignRequestController.php",
    "Admin/ReviewController.php",
    "Admin/NotificationController.php",
    "User/ProductController.php",
    "User/CategoryController.php",
    "User/OrderController.php",
    "User/CustomDesignRequestController.php",
    "User/ReviewController.php",
    "User/NotificationController.php",
]

REQUESTS = [
    "Admin/Product/StoreProductRequest.php",
    "Admin/Product/UpdateProductRequest.php",
    "Admin/Product/UpdateStockRequest.php",
    "Admin/Category/StoreCategoryRequest.php",
    "Admin/Category/UpdateCategoryRequest.php",
    "User/Order/StoreOrderRequest.php",
    "User/Review/StoreReviewRequest.php",
]

RESOURCES = [
    "ProductResource.php",
    "CategoryResource.php",
    "OrderResource.php",
    "ReviewResource.php",
    "NotificationResource.php",
]

POLICIES = [
    "ProductPolicy.php",
    "CategoryPolicy.php",
    "OrderPolicy.php",
    "ReviewPolicy.php",
    "CustomDesignRequestPolicy.php",
]

SERVICES = [
    "ProductService.php",
    "OrderService.php",
    "InventoryService.php",
    "NotificationService.php",
]

def create_file(path, content):
    file_path = ROOT / path
    file_path.parent.mkdir(parents=True, exist_ok=True)
    file_path.write_text(content, encoding="utf-8")

def main():
    for path, content in FILES.items():
        create_file(path, content)

    for item in CONTROLLERS:
        create_file(
            "app/Http/Controllers/Api/" + item,
            "<?php\n\n// Laravel 12 Controller generated location\n"
        )

    for item in REQUESTS:
        create_file(
            "app/Http/Requests/" + item,
            "<?php\n\n// Form Request generated location\n"
        )

    for item in RESOURCES:
        create_file(
            "app/Http/Resources/" + item,
            "<?php\n\n// API Resource generated location\n"
        )

    for item in POLICIES:
        create_file(
            "app/Policies/" + item,
            "<?php\n\n// Policy generated location\n"
        )

    for item in SERVICES:
        create_file(
            "app/Services/" + item,
            "<?php\n\n// Service Layer generated location\n"
        )

    zip_name = "laravel_controllers_architecture.zip"

    with zipfile.ZipFile(zip_name, "w", zipfile.ZIP_DEFLATED) as z:
        for file in ROOT.rglob("*"):
            if file.is_file():
                z.write(file, file)

    print(f"Created: {zip_name}")

if __name__ == "__main__":
    main()
