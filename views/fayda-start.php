<div class="card">
    <div class="card-header">የስራ ፈላጊ ምዝገባ — በፋይዳ</div>
    <div class="card-body">
        <form method="get" action="<?= rtrim($_ENV['BASE_URL'], '/') ?>/index.php">
            <input type="hidden" name="action" value="fayda-redirect">
            <div class="form-group">
                <label>የመታወቂያ ቁጥር (ID Number)</label>
                <input type="text" name="id_number" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">ወደ ፋይዳ ማረጋገጫ ይቀጥሉ</button>
        </form>
    </div>
</div>