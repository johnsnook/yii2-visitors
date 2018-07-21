<?php
/**
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div class="offset-3 col-md-6">
    <form>
        <div class="input-group mb-3">
            <label for="ip">IP Address</label>
            <input type="text" class="form-control" id="ip" placeholder="0.0.0.0">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="button" onclick="">Get</button>
            </div>

        </div>
        <div class="form-group">
            <label for="ipInfo">IP Info</label>
            <textarea class="form-control" id="ipInfo" rows="15" readonly></textarea>
        </div>

    </form>
</div>