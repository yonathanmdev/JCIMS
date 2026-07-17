<?php $is_sra_edl_page = true; ?>
<section class="content">
  <div class="container-fluid">
    <div class="card card-default">
      <div class="card card-primary card-outline">
        <div class="card-body">
<div class="row mb-3">
    <div class="col-md-6">
    <h1 class="h3 mb-0 text-gray-800">ስራ እድል የተፈጠረላቸዉን ዝርዝር  </h1>
    </div>  
    <div class="container mt-4">
    <div class="card">
       
        <div class="card-body">
            <table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>ተ.ቁ</th>
            <th>የስራ ፈላጊ መለያ</th>
            <th>የስራ ዘርፍ</th>
            <th>ተግባር</th>
        </tr>
    </thead>
    <tbody>
        <?php if(!empty($jobCreations)){

        ?>
        <?php foreach ($jobCreations as $jobCreations => $row): ?>
        <tr>
            <td><?= $jobCreations + 1 ?></td>
            <td><?= htmlspecialchars($row['jobseeker_id']) ?></td>
            <td><?= htmlspecialchars($row['sector']) ?></td>
            <td>
                <!-- መሰረዝ (Delete) 
                <a href="/job-creation-delete?uuid=<?= bin2hex($row['uuid']) ?>" 
                   class="btn btn-danger btn-sm" 
                   onclick="return confirm('እርግጠኛ ነዎት መሰረዝ ይፈልጋሉ?')">
                   ሰርዝ
                </a>Action -->
            </td>
        </tr>
        <?php endforeach;
        }
        ?>
    </tbody>
</table>
        </div>
    </div>
</div>
</div>
</div> 
</div> 
</div> 
</section>
 
 