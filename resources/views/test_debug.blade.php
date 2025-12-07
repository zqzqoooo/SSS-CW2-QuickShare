<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>QuickShare Core System Debug Console (核心系统调试台)</title>
    <link rel="icon" type="image/svg+xml" href="/logo.svg">
    <style>
        body { background-color: #f3f4f6; font-family: 'Segoe UI', monospace; padding: 30px; color: #1f2937; }
        .container { max-width: 1100px; margin: 0 auto; }
        
        /* New Red Panel Style (新增红色面板样式) */
        .btn-red:hover { background: #dc2626; }
        
        /* Layout Grid (布局网格) */
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-bottom: 25px; }
        .panel { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); border-top: 4px solid transparent; }
        .panel-blue { border-top-color: #3b82f6; }
        .panel-green { border-top-color: #10b981; }
        .panel-purple { border-top-color: #8b5cf6; }
        .panel-red { border-top-color: #ef4444; }

        
        h2 { border-bottom: 1px solid #eee; padding-bottom: 15px; margin-top: 0; font-size: 1.1em; color: #4b5563; display: flex; justify-content: space-between; align-items: center; }
        p { font-size: 0.9em; color: #6b7280; }

        /* Button General Style (按钮通用样式) */
        .btn { padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; color: white; font-weight: bold; font-size: 13px; transition: 0.2s; text-decoration: none; display: inline-block; }
        .btn:hover { opacity: 0.9; }
        .btn-blue { background: #3b82f6; }
        .btn-green { background: #10b981; }
        .btn-purple { background: #8b5cf6; }
        .btn-red { background: #ef4444; }
        .btn-yellow { background: #f59e0b; }
        .btn-gray { background: #6b7280; }


        /* Form Elements (表单元素) */
        input[type="text"], input[type="file"] { width: 100%; padding: 8px; margin: 8px 0; border: 1px solid #d1d5db; border-radius: 4px; box-sizing: border-box; font-family: monospace; }
        label { font-weight: bold; font-size: 0.85em; color: #374151; }

        /* Result Display Box (结果展示黑框) */
        .result-box { background: #111827; color: #34d399; padding: 15px; border-radius: 6px; margin-top: 15px; font-size: 12px; overflow-x: auto; font-family: 'Courier New', Courier, monospace; }
        .result-title { color: #9ca3af; margin-bottom: 5px; border-bottom: 1px dashed #4b5563; padding-bottom: 5px; display: block; }
        
        /* Table Style (表格样式) */
        table { width: 100%; border-collapse: collapse; color: #e5e7eb; }
        th, td { border: 1px solid #374151; padding: 6px 10px; text-align: left; }
        th { background-color: #374151; color: #9ca3af; font-weight: normal; }
        .highlight { color: #fcd34d; font-weight: bold; font-size: 1.1em; }
        
        /* Status Tags (状态标签) */
        .badge { padding: 2px 6px; border-radius: 3px; font-size: 10px; color: white; }
        .bg-user { background: #3b82f6; }
        .bg-guest { background: #6b7280; }
        .text-expired { color: #ef4444; font-weight: bold; }
        .text-active { color: #10b981; }

        /* Stats Card (统计卡片) */
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 20px; }
        .stat-card { background: #f9fafb; padding: 15px; border-radius: 6px; text-align: center; border: 1px solid #e5e7eb; }
        .stat-title { font-size: 11px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-value { font-size: 20px; font-weight: bold; color: #1f2937; margin-top: 5px; }
    </style>
</head>
<body>

<div class="container">
    <div style="text-align: center; margin-bottom: 30px;">
        <h1 style="margin-bottom: 10px;">🛠️ QuickShare Core System Debug Console (核心系统调试台)</h1>
        <a href="{{ route('debug.index') }}" class="btn btn-gray">🔄 Refresh Page / Reset State (刷新页面 / 重置状态)</a>
    </div>

    @if($errors->any())
        <div style="background: #fee2e2; border-left: 4px solid #ef4444; color: #b91c1c; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
            <strong>❌ Operation Failed (操作失败)：</strong> {{ $errors->first() }}
        </div>
    @endif
    
    @if(session('status'))
        <div style="background: #d1fae5; border-left: 4px solid #10b981; color: #065f46; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
            <strong>✅ System Notification (系统通知)：</strong> {{ session('status') }}
        </div>
    @endif
    <div class="grid">
        
        <div class="panel panel-blue">
            <h2>1. Code Manager Service (码库服务)</h2>
            <p>Test the underlying share code allocation and recycling mechanism (does not involve file storage) (测试底层的取件码分配与回收机制 (不涉及文件存储))。</p>
            
            <div style="margin-top: 20px;">
                <form action="{{ route('debug.code.get') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-blue" style="width: 100%">API: getNextAvailableCode()</button>
                </form>
            </div>

            @if(isset($section) && $section == 'code')
                <div class="result-box">
                    <span class="result-title">Operation Result (操作结果)</span>
                    <p>Action (动作): <strong>{{ $action }}</strong></p>
                    @if($dbRecord)
                        <table>
                            <tr><th>Code (取件码)</th><td class="highlight">{{ $dbRecord->code }}</td></tr>
                            <tr><th>Is Used (是否占用)</th><td>{{ $dbRecord->is_used ? 'TRUE (已占用)' : 'FALSE (空闲)' }}</td></tr>
                            <tr><th>Updated (更新时间)</th><td>{{ $dbRecord->updated_at }}</td></tr>
                        </table>
                    @else
                         <p style="color: #ef4444">Record Not Found or Code Pool Full (未找到记录或码池已满)。</p>
                    @endif
                </div>

                @if($action == 'get')
                    <div style="margin-top: 15px;">
                        <form action="{{ route('debug.code.recycle') }}" method="POST">
                            @csrf
                            <input type="hidden" name="code" value="{{ $code }}">
                            <button type="submit" class="btn btn-purple" style="width: 100%">API: recycleCode('{{ $code }}')</button>
                        </form>
                    </div>
                @endif
            @endif
        </div>

        <div class="panel panel-green">
            <h2>2. File Manager Service (文件服务)</h2>
            <p>Test the complete upload, storage, expiration calculation, and download process (测试完整的上传、存储、过期计算与下载流程)。</p>

            <div style="background: #f0fdf4; padding: 15px; border-radius: 6px; border: 1px dashed #10b981; margin-bottom: 20px;">
                <form action="{{ route('debug.file.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <label>📤 File Upload Test (上传文件测试)</label>
                    <input type="file" name="file" required>
                    <button type="submit" class="btn btn-green" style="width: 100%">API: uploadFile()</button>
                </form>

                @if(isset($section) && $section == 'file' && isset($fileRecord))
                    <div class="result-box">
                        <span class="result-title">Database Record (files table) (数据库记录)</span>
                        <table>
                            <tr><th>ID</th><td>{{ $fileRecord->id }}</td></tr>
                            <tr><th>Code (取件码)</th><td class="highlight">{{ $fileRecord->share_code }}</td></tr>
                            <tr><th>Name (文件名)</th><td>{{ $fileRecord->original_name }}</td></tr>
                            <tr><th>Expires (过期时间)</th><td>{{ $fileRecord->expires_at }}</td></tr>
                            <tr><th>User (用户ID)</th><td>{{ $fileRecord->user_id ? 'User:'.$fileRecord->user_id : 'Guest (NULL) (访客)' }}</td></tr>
                        </table>
                    </div>
                    <p style="font-size: 12px; color: #059669; margin-top: 5px;">* Copy the Code above for download test (请复制上方 Code 用于下载测试)</p>
                @endif
            </div>

            <div>
                <form action="{{ route('debug.file.download') }}" method="GET" target="_blank">
                    <label>📥 File Download Test (下载文件测试)</label>
                    <input type="text" name="code" placeholder="Enter 6-digit Share Code (输入 6 位取件码)" required style="text-transform: uppercase;">
                    <button type="submit" class="btn btn-blue" style="width: 100%">API: findFileByCode() & Download</button>
                </form>
            </div>
        </div>

    </div>
    
    <div class="panel panel-purple">
        <h2>
            <span>3. Admin Data Dashboard (管理员数据看板)</span>
            <div>
                <form action="{{ route('debug.admin.cleanup') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-yellow" title="Simulate Cron Job (模拟 Cron Job)">⚡ Trigger Cleanup Task (触发清理任务)</button>
                </form>
                <a href="{{ route('debug.admin.list') }}" class="btn btn-purple" style="margin-left: 5px;">🔄 Refresh Data (刷新数据)</a>
            </div>
        </h2>

        @if(isset($section) && $section == 'admin' && isset($stats))
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-title">📦 Total Historical Uploads (历史总上传)</div>
                    <div class="stat-value">{{ $stats['total_uploads'] }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-title">💾 Cumulative Traffic (累计流量)</div>
                    <div class="stat-value" style="color: #3b82f6;">{{ $stats['total_size'] }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-title">👤 User Ratio (用户占比)</div>
                    <div class="stat-value" style="color: #8b5cf6;">{{ $stats['user_ratio'] }}%</div>
                </div>
                <div class="stat-card">
                    <div class="stat-title">🟢 Currently Active (当前活跃)</div>
                    <div class="stat-value" style="color: #10b981;">{{ $occupiedCodes->count() }}</div>
                </div>
            </div>

            <h3 style="font-size: 14px; color: #374151; margin-bottom: 10px; border-left: 3px solid #8b5cf6; padding-left: 10px;">List of Currently Occupied Codes (当前占用取件码列表)</h3>
            
            @if($occupiedCodes->count() > 0)
                <div class="result-box" style="max-height: 350px; overflow-y: auto; background: #1f2937;">
                    <table>
                        <thead>
                            <tr>
                                <th>Share Code (取件码)</th>
                                <th>Expiration Status (过期状态)</th>
                                <th>Associated File (关联文件)</th>
                                <th>Owner (归属)</th>
                                <th>Action (操作)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($occupiedCodes as $item)
                                @php
                                    // Simple query for associated file in the view for demonstration (在视图中简单查询关联文件以展示信息)
                                    $fileInfo = \App\Models\File::where('share_code', $item->code)->first();
                                @endphp
                                <tr>
                                    <td class="highlight">{{ $item->code }}</td>
                                    <td>
                                        @if($fileInfo)
                                            @if(now()->greaterThan($fileInfo->expires_at))
                                                <span class="text-expired">Expired (已过期 (待清理))</span>
                                            @else
                                                <span class="text-active">{{ $fileInfo->expires_at->diffForHumans() }}</span>
                                            @endif
                                        @else
                                            <span style="color: #6b7280;">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($fileInfo)
                                            {{ $fileInfo->original_name }} <span style="color: #6b7280;">({{ $fileInfo->id }})</span>
                                        @else
                                            <span class="text-expired">No File (Orphan Code) (无文件 (孤儿码))</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($fileInfo)
                                            @if($fileInfo->user_id) 
                                                <span class="badge bg-user">User (用户)</span> 
                                            @else 
                                                <span class="badge bg-guest">Guest (访客)</span> 
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td style="text-align: center;">
                                        <form action="{{ route('debug.admin.delete') }}" method="POST" onsubmit="return confirm('Confirm mandatory deletion? This action will archive the record and recycle the share code. (确定强制删除？此操作将归档记录并回收取件码。)');">
                                            @csrf
                                            <input type="hidden" name="code" value="{{ $item->code }}">
                                            <button type="submit" style="background: none; border: none; cursor: pointer; color: #ef4444; font-weight: bold;">🗑️ Delete (删除)</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="text-align: center; padding: 30px; background: #f9fafb; border: 1px dashed #d1d5db; color: #6b7280; border-radius: 6px;">
                    ✅ System is currently idle, no active files. (系统当前空闲，没有活跃文件。)
                </div>
            @endif

        @else
            <div style="text-align: center; padding: 40px; color: #9ca3af;">
                Please click the top-right <span style="color: #8b5cf6; font-weight: bold;">🔄 Refresh Data (刷新数据)</span> button to load the dashboard. (请点击右上方 🔄 刷新数据 加载看板。)
            </div>
        @endif
    </div>
    
    <div class="panel panel-red">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px;">
            <h2 style="margin: 0; border: none; color: #ef4444;">4. User Ban Management (用户封禁管理)</h2>
            <a href="{{ route('debug.users.list') }}" class="btn btn-red" style="width: auto; margin: 0;">👥 Load User List (加载用户列表)</a>
        </div>

        @if(isset($section) && $section == 'users')
            <div class="result-box" style="background: #fff; color: #333; border: 1px solid #e5e7eb;">
                @if(isset($users) && count($users) > 0)
                    <table style="color: #374151;">
                        <thead>
                            <tr style="background: #f9fafb; border-bottom: 2px solid #e5e7eb;">
                                <th>ID</th>
                                <th>Username (用户名)</th>
                                <th>Email (邮箱)</th>
                                <th>Role (角色)</th>
                                <th>Current Status (当前状态)</th>
                                <th>Management Action (管理操作)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr style="border-bottom: 1px solid #f3f4f6;">
                                    <td>{{ $user->id }}</td>
                                    <td style="font-weight: bold;">{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if($user->is_admin) 
                                            <span class="badge" style="background: #8b5cf6;">Admin (管理员)</span> 
                                        @else 
                                            <span class="badge" style="background: #6b7280;">Regular User (普通用户)</span> 
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->is_banned)
                                            <span class="badge" style="background: #ef4444;">🚫 Banned (已封禁)</span>
                                        @else
                                            <span class="badge" style="background: #10b981;">✅ Normal (正常)</span>
                                        @endif
                                    </td>
                                    <td>
                                        <form action="{{ route('debug.users.ban') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                                            @if($user->is_banned)
                                                <button type="submit" class="btn btn-green" style="padding: 4px 10px; font-size: 11px; width: auto; margin: 0;">🔓 Unban User (解封用户)</button>
                                            @else
                                                <button type="submit" class="btn btn-red" style="padding: 4px 10px; font-size: 11px; width: auto; margin: 0;" onclick="return confirm('Are you sure you want to ban this user? He/She will not be able to log in. (确定要封禁该用户吗？他将无法登录。)')">🚫 Ban Immediately (立即封禁)</button>
                                            @endif
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div style="text-align: center; padding: 30px; color: #9ca3af;">
                        <p>There are currently no other regular users in the system. (系统中暂时没有其他普通用户。)</p>
                        <p style="font-size: 0.9em;">Please register a few test accounts at /register first. (请先去 /register 注册几个测试账号再来查看。)</p>
                    </div>
                @endif
            </div>
        @else
            <div style="text-align: center; padding: 20px; color: #6b7280;">
                Click the red button on the top right to load the list. (点击右上角红色按钮加载列表。)
            </div>
        @endif
    </div>
    <div class="panel" style="margin-top: 20px; border-top: 4px solid #f59e0b;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px;">
            <h2 style="margin: 0; border: none; color: #f59e0b;">5. SMTP Diagnostics (邮件服务诊断)</h2>
        </div>

        <div style="background: #fffbeb; padding: 20px; border-radius: 6px; border: 1px solid #fcd34d;">
            <p style="margin-top: 0; color: #92400e;">Test whether the SMTP configuration is correct here (Outlook/Gmail/Mailtrap). If it fails, check the .env file. (在此测试 SMTP 配置是否正确 (Outlook/Gmail/Mailtrap)。如果不通，请检查 .env 文件。)</p>
            
            <form action="{{ route('debug.email.send') }}" method="POST" style="display: flex; gap: 10px; align-items: center;">
                @csrf
                <div style="flex-grow: 1;">
                    <input type="text" name="email" placeholder="Enter the email address to receive the test email... (请输入接收测试邮件的邮箱地址...)" required style="margin: 0;">
                </div>
                <button type="submit" class="btn btn-yellow" style="width: auto; margin: 0;">📨 Send Test Email (发送测试邮件)</button>
            </form>

            <div style="margin-top: 15px; font-size: 12px; color: #b45309;">
                <strong>Current Configuration Check (.env) (当前配置检查):</strong><br>
                MAIL_MAILER: <code>{{ env('MAIL_MAILER') }}</code> | 
                HOST: <code>{{ env('MAIL_HOST') }}</code> | 
                PORT: <code>{{ env('MAIL_PORT') }}</code> | 
                ENCRYPTION: <code>{{ env('MAIL_ENCRYPTION') }}</code>
            </div>
        </div>
    </div>

    <div style="text-align: center; margin-top: 40px; color: #9ca3af; font-size: 0.8em;">
        QuickShare Debug Console v1.0 &copy; {{ date('Y') }}
    </div>
</div>

</body>
</html>