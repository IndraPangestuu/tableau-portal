<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DatabaseBackupService;
use Illuminate\Http\Request;

class BackupController extends Controller
{
    protected $backupService;
    
    public function __construct(DatabaseBackupService $backupService)
    {
        $this->backupService = $backupService;
    }
    
    /**
     * Display backup management page
     */
    public function index()
    {
        $backups = $this->backupService->listBackups();
        return view('admin.backups.index', compact('backups'));
    }
    
    /**
     * Create new backup
     */
    public function store()
    {
        $result = $this->backupService->backup();
        
        if ($result['success']) {
            return redirect()->route('backups.index')
                ->with('success', "Backup berhasil dibuat: {$result['filename']} ({$result['size']})");
        }
        
        return redirect()->route('backups.index')
            ->with('error', 'Gagal membuat backup database');
    }
    
    /**
     * Download backup file
     */
    public function download(string $filename)
    {
        $path = $this->backupService->getBackupPath($filename);
        
        if (!$path) {
            return redirect()->route('backups.index')
                ->with('error', 'File backup tidak ditemukan');
        }
        
        return response()->download($path);
    }
    
    /**
     * Delete backup file
     */
    public function destroy(string $filename)
    {
        if ($this->backupService->delete($filename)) {
            return redirect()->route('backups.index')
                ->with('success', 'Backup berhasil dihapus');
        }
        
        return redirect()->route('backups.index')
            ->with('error', 'Gagal menghapus backup');
    }
    
    /**
     * Restore database from backup
     */
    public function restore(string $filename)
    {
        $result = $this->backupService->restore($filename);
        
        if ($result['success']) {
            return redirect()->route('backups.index')
                ->with('success', $result['message']);
        }
        
        return redirect()->route('backups.index')
            ->with('error', $result['message']);
    }
}