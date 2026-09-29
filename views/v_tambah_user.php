<?php require_once __DIR__ . '/_helpers.php'; $current_page_override = 'v_user.php'; $basePath = base_url(); ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tambah User - SiParkur</title>

    <link rel="stylesheet" href="<?=e($basePath . '/asset/dashboard_admin.css')?>">
</head>

<body>

<div class="layout">

    <?php include __DIR__ . '/_sidebar.php'; ?>

    <main class="main">

        <!-- HEADER -->
        <div class="header">

            <div>

                <div class="small-title">
                    MANAJEMEN USER
                </div>

                <h1>
                    Tambah User
                </h1>

                <p class="subtitle">
                    Tambahkan user baru ke sistem SiParkur
                </p>

            </div>

        </div>


        <!-- FORM -->
        <div class="page-panel">

            <div class="page-title-row">

                <div>

                    <h3>
                        Form User
                    </h3>

                    <p>
                        Isi data user dengan lengkap
                    </p>

                </div>

            </div>


            <form action="<?=e($basePath . '/controller/c_user.php?aksi=proses_tambah')?>"
                  method="POST">


                <div class="form-group">

                    <label>
                        Nama
                    </label>

                    <input
                        type="text"
                        name="nama"
                        class="form-input"
                        placeholder="Masukkan nama lengkap"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>
                        Username
                    </label>

                    <input
                        type="text"
                        name="username"
                        class="form-input"
                        placeholder="Masukkan username"
                        autocomplete="off"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>
                        Password
                    </label>

                    <input
                        type="password"
                        name="password"
                        class="form-input"
                        placeholder="Masukkan password"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>
                        Role
                    </label>

                    <select
                        name="role"
                        class="form-select"
                        required
                    >

                        <option value="">
                            -- Pilih Role --
                        </option>

                        <option value="admin">
                            Admin
                        </option>

                        <option value="petugas">
                            Petugas
                        </option>

                        <option value="owner">
                            Owner
                        </option>

                    </select>

                </div>


                <div class="form-actions">

                    <button
                        type="submit"
                        class="btn-primary"
                    >
                        Simpan
                    </button>

                    <a
                        href="<?=e($basePath . '/controller/c_user.php?aksi=tampil')?>"
                        class="btn-secondary"
                    >
                        Kembali
                    </a>

                </div>


            </form>

        </div>

    </main>

</div>

</body>

</html>