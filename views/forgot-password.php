<style>
    .auth-card {
        max-width: 400px; 
        margin: 50px auto; 
        border-radius: 15px;
        transition: transform 0.3s ease;
    }
    .auth-card:hover { transform: translateY(-5px); }
    .btn-custom { border-radius: 50px; font-weight: bold; }
</style>

<div class="container">
    <!-- የመጀመሪያው ቅጽ -->
    <div class="card shadow-sm auth-card">
        <div class="card-body p-4">
            <h4 class="text-center mb-4 text-primary">የይለፍ ቃል ማግኛ</h4>
            <form method="POST">
                <input type="hidden" name="send_code" value="1"> <!-- ይህንን አክለናል -->
                <div class="mb-3">
                    <label class="form-label">ስልክ ቁጥርዎን ያስገቡ</label>
                  <!--   <input type="text" name="phone" class="form-control" placeholder="09xxxxxxxx" required>
             -->    </div>
            <!--     <button type="submit" class="btn btn-success w-100 btn-custom">ኮድ ላክልኝ</button>
        -->     </form>
        </div>
    </div>

    <!-- ሁለተኛው ቅጽ (በተለየ ክፍል ወይም በPHP if condition ሊያሳዩት ይችላሉ) -->
    <div class="card shadow-sm auth-card">
        <div class="card-body p-4">
            <h4 class="text-center mb-4 text-primary">አዲስ የይለፍ ቃል</h4>
            <form method="POST">
                <input type="hidden" name="change_password" value="1"> <!-- ይህንን አክለናል -->
              <!--   <div class="mb-3">
                    <label class="form-label">የተላከውን ኮድ ያስገቡ</label>
                    <input type="text" name="token" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">አዲስ የይለፍ ቃል</label>
                    <input type="password" name="new_password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 btn-custom">ይለፍ ቃል ቀይር</button>
            </form> -->
        </div>
    </div>
</div>