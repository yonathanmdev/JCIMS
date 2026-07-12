<!-- Edit Defense Applicant Modal -->
<div class="modal fade" id="editDefenseModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            
            <form action="<?= rtrim($_ENV['BASE_URL'], '/') ?>/processEdit" method="POST" id="editDefenseForm" data-client-validate-edit="true" novalidate>
                <!-- Security Tokens & Hidden ID -->
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                <input type="hidden" name="defense_id" id="edit_defense_id" value="">

                <!-- Modal Header -->
                <div class="modal-header bg-warning text-dark">
                    <h6 class="modal-title font-weight-bold" id="editModalLabel">
                        <i class="fas fa-user-edit mr-1"></i>
                        የአገር መከላከያ ሰራዊት ምዝገባ ማስተካከያ
                    </h6>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    
                    <!-- 1. ሙሉ ስም እና ብሄራዊ መለያ -->
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group mb-2">
                                <label for="edit_fullname"><small class="font-weight-bold">ሙሉ ስም (የአያት ስምን ጨምሮ)</small></label>
                                <input type="text" class="form-control form-control-sm" id="edit_fullname" name="fullname" maxlength="100" required>
                                <div class="invalid-feedback">እባክዎ ሙሉ ስም ያስገቡ።</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-2">
                                <label for="edit_national_id"><small class="font-weight-bold">ብሄራዊ መለያ (Fayda ID)</small> <span class="text-muted text-xs">(አማራጭ)</span></label>
                                <input type="text" class="form-control form-control-sm" id="edit_national_id" name="national_id" maxlength="50">
                            </div>
                        </div>
                    </div>

                    <!-- 2. ጾታ፣ እድሜ እና ስልክ ቁጥር -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group mb-2">
                                <label for="edit_sex"><small class="font-weight-bold">ጾታ</small></label>
                                <select class="form-control form-control-sm" id="edit_sex" name="sex" required>
                                    <option value="">-- ይምረጡ --</option>
                                    <option value="ወንድ">ወንድ</option>
                                    <option value="ሴት">ሴት</option>
                                </select>
                                <div class="invalid-feedback">እባክዎ ጾታ ይምረጡ።</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-2">
                                <label for="edit_age"><small class="font-weight-bold">እድሜ</small></label>
                                <input type="number" class="form-control form-control-sm" id="edit_age" name="age" min="18" max="30" required>
                                <div class="invalid-feedback">እባክዎ ትክክለኛ እድሜ ያስገቡ (18-30)።</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-2">
                                <label for="edit_phone"><small class="font-weight-bold">ስልክ ቁጥር</small></label>
                                <input type="tel" class="form-control form-control-sm" id="edit_phone" name="phone" placeholder="09..." maxlength="10" required>
                                <div class="invalid-feedback">እባክዎ ስልክ ቁጥር ያስገቡ።</div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. የትምህርት ደረጃ እና መስክ -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-2">
                                <label for="edit_education_level"><small class="font-weight-bold">የትምህርት ደረጃ</small></label>
                                <select class="form-control form-control-sm" id="edit_education_level" name="education_level" required>
                                    <option value="">-- ይምረጡ --</option>
                                    <option value="8ኛ ክፍል ያጠናቀቀ">8ኛ ክፍል ያጠናቀቀ</option>
                                    <option value="10ኛ/12ኛ ክፍል">10ኛ/12ኛ ክፍል ያጠናቀቀ</option>
                                    <option value="ሰርተፊኬት (Level I/II)">ሰርተፊኬት (Level I/II)</option>
                                    <option value="ዲፕሎማ (Level III/IV)">ዲፕሎማ (Level III/IV)</option>
                                    <option value="የመጀመሪያ ዲግሪ">የመጀመሪያ ዲግሪ</option>
                                    <option value="ከዛ በላይ">ከመጀመሪያ ዲግሪ በላይ</option>
                                </select>
                                <div class="invalid-feedback">እባክዎ የትምህርት ደረጃ ይምረጡ።</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6" id="edit_study_field_wrapper" style="display: none;">
                            <div class="form-group mb-2">
                                <label for="edit_educated_study"><small class="font-weight-bold">የትምህርት መስክ / ሙያ</small></label>
                                <input type="text" class="form-control form-control-sm" id="edit_educated_study" name="educated_study" placeholder="ምሳሌ፦ ማህበራዊ ሳይንስ፣ መካኒክ...">
                                <div class="invalid-feedback">እባክዎ የትምህርት መስክ ያስገቡ።</div>
                            </div>
                        </div>
                    </div>

                    <!-- 4. ተጨማሪ ክህሎት እና ዘርፍ -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-2">
                                <label for="edit_additional_skill"><small class="font-weight-bold">ተጨማሪ ክህሎት (ካለ)</small></label>
                                <input type="text" class="form-control form-control-sm" id="edit_additional_skill" name="additional_skill">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-2">
                                <label for="edit_sector"><small class="font-weight-bold">ምድብ / ሴክተር</small></label>
                                <select class="form-control form-control-sm" id="edit_sector" name="sector" required>
                                    <option value="">-- ይምረጡ --</option>
                                    <option value="መደበኛ ሰራዊት">ለመደበኛ ሰራዊት</option>
                                    <option value="አይ ሲቲ ሙያተኞች">ለአይ ሲቲ ሙያተኞች</option>
                                    <option value="እጩ መኮንን">ለእጩ መኮንን</option>
                                    <option value="ህግ ሙያተኞች">ለህግ ሙያተኞች</option>
                                    <option value="መሃንድስ ሙያተኞች">ለመሃንድስ ሙያተኞች</option>
                                    <option value="ጤና ሙያተኞች">ለጤና ሙያተኞች</option>
                                    <option value="ሎጀስቲክስ ሙያተኞች">ለተለያዩ የሎጀስቲክስ ሙያተኞች</option>
                                    <option value="ኢንጂነሪንግ">ለኢንጂነሪንግ</option>
                                    <option value="ጤና ሳይንስ">ለጤና ሳይንስ</option>
                                    <option value="ሪሶርስ ማናጅመንት">ለሪሶርስ ማናጅመንት</option>   
                                </select>
                                <div class="invalid-feedback">እባክዎ ሴክተር ይምረጡ።</div>
                            </div>
                        </div>
                    </div>

                    <!-- 5. ቀበሌ -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group mb-2">
                                <label for="edit_kebele"><small class="font-weight-bold">የመኖሪያ ቀበሌ</small></label>
                                <input type="text" class="form-control form-control-sm" id="edit_kebele" name="kebele" required>
                                <div class="invalid-feedback">እባክዎ ቀበሌ ያስገቡ።</div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Modal Footer -->
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">ዝጋ</button>
                    <button type="submit" class="btn btn-warning btn-sm text-dark font-weight-bold">
                        <i class="fas fa-save mr-1"></i> ማስተካከያውን አጽድቅ
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>