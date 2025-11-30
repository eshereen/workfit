# PHP Memory Limit Guide for WorkFit

## Recommended Memory Limits

### **For Normal Application Runtime: 256M-512M** ✅
- Your current **512M** is perfect for normal operations
- Laravel applications typically need 128M-256M
- With Filament + your packages, 512M is safe and generous

### **For Composer Operations: 512M-1024M** ⚠️
- Composer autoload dump can be memory-intensive
- Package installations need more memory temporarily
- **Only needed during deployment/maintenance**

### **For Heavy Operations (Exports/PDFs): 256M-512M** ✅
- Excel exports: ~256M should be enough
- PDF generation: ~512M is safe
- Your current limit handles these well

## Recommended Setup

### **Option 1: Keep 512M for Runtime, Increase Only for Composer** (Recommended)
```bash
# In .user.ini or php.ini (for normal operations)
memory_limit = 512M

# For composer operations, use command-line override:
php -d memory_limit=1024M /opt/cpanel/composer/bin/composer dump-autoload
php -d memory_limit=1024M artisan package:discover
```

### **Option 2: Set Higher Limit Permanently** (If Option 1 Doesn't Work)
```bash
# In .user.ini or php.ini
memory_limit = 1024M
```

## When to Increase Beyond 512M?

**Only if you experience:**
- ❌ Composer operations still failing with 512M
- ❌ Large Excel/PDF exports failing
- ❌ Memory errors in application logs

**Don't increase if:**
- ✅ Application runs fine
- ✅ Only composer post-install scripts failed (one-time issue)
- ✅ No runtime memory errors

## Your Current Situation

Based on your error, the issue was **only during composer autoload dump** (one-time operation). 

**Recommendation:**
1. **Keep 512M for runtime** - it's perfect for your application
2. **Use 1024M temporarily for composer operations** when needed:
   ```bash
   php -d memory_limit=1024M composer dump-autoload
   ```

## Memory Usage by Operation

| Operation | Typical Memory Need | Your Setting |
|-----------|-------------------|--------------|
| Normal page load | 50-128M | ✅ 512M (plenty) |
| Filament admin | 128-256M | ✅ 512M (plenty) |
| Excel export | 128-256M | ✅ 512M (plenty) |
| PDF generation | 256-512M | ✅ 512M (sufficient) |
| Composer dump-autoload | 512M-1GB | ⚠️ May need 1024M |
| Package install | 512M-1GB | ⚠️ May need 1024M |

## Final Recommendation

**Keep 512M for runtime** - it's more than enough for your Laravel + Filament application.

**For composer operations**, use temporary override:
```bash
php -d memory_limit=1024M composer [command]
```

This way you don't waste server resources on normal operations, but have enough when needed for maintenance tasks.

