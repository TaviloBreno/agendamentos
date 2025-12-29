<?= $this->extend('back/layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <a href="<?= route_to('admin.messages') ?>" class="text-decoration-none text-gray-800">
            <i class="fas fa-arrow-left mr-2"></i>
        </a>
        <?= esc($title) ?>
    </h1>
    <div class="dropdown">
        <button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown">
            <i class="fas fa-ellipsis-v"></i>
        </button>
        <div class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="#" onclick="toggleMute()">
                <i class="fas fa-bell-slash mr-2"></i> Silenciar
            </a>
            <?php if ($conversation->type === 'group'): ?>
                <div class="dropdown-divider"></div>
                <form action="<?= route_to('admin.messages.leave', $conversation->id) ?>" method="POST">
                    <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Tem certeza que deseja sair do grupo?')">
                        <i class="fas fa-sign-out-alt mr-2"></i> Sair do Grupo
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <!-- Chat Messages -->
            <div class="card-body chat-container" id="chatContainer" style="height: 500px; overflow-y: auto;">
                <div id="messagesContainer">
                    <?php if (empty($messages)): ?>
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-comments fa-3x mb-3"></i>
                            <p>Nenhuma mensagem ainda. Comece a conversa!</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($messages as $message): ?>
                            <?php if ($message->is_system): ?>
                                <!-- System Message -->
                                <div class="text-center my-3">
                                    <span class="badge badge-secondary px-3 py-2">
                                        <?= esc($message->message) ?>
                                    </span>
                                </div>
                            <?php else: ?>
                                <?php $isMine = $message->sender_id === $userId; ?>
                                <div class="d-flex mb-3 <?= $isMine ? 'justify-content-end' : '' ?>">
                                    <?php if (!$isMine): ?>
                                        <?php $sender = $message->getSender(); ?>
                                        <img src="<?= $sender ? $sender->avatarUrl(40) : '' ?>" 
                                             class="rounded-circle mr-2" width="40" height="40" alt="Avatar">
                                    <?php endif; ?>
                                    
                                    <div class="message-bubble <?= $isMine ? 'message-mine' : 'message-other' ?>">
                                        <?php if (!$isMine && $conversation->type === 'group'): ?>
                                            <div class="message-sender small font-weight-bold mb-1">
                                                <?= esc($sender->name ?? 'Usuário') ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($message->hasAttachment()): ?>
                                            <div class="message-attachment mb-2">
                                                <?php if ($message->isImageAttachment()): ?>
                                                    <a href="<?= $message->getAttachmentUrl() ?>" target="_blank">
                                                        <img src="<?= $message->getAttachmentUrl() ?>" class="img-fluid rounded" style="max-width: 200px;">
                                                    </a>
                                                <?php else: ?>
                                                    <a href="<?= $message->getAttachmentUrl() ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas <?= $message->getAttachmentIcon() ?> mr-1"></i>
                                                        Baixar anexo
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($message->message): ?>
                                            <div class="message-text"><?= $message->getFormattedMessage() ?></div>
                                        <?php endif; ?>
                                        
                                        <div class="message-time small text-muted text-right">
                                            <?= $message->timeAgo() ?>
                                        </div>
                                    </div>
                                    
                                    <?php if ($isMine): ?>
                                        <?php $currentUser = session('user'); ?>
                                        <img src="<?= $currentUser ? $currentUser->avatarUrl(40) : '' ?>" 
                                             class="rounded-circle ml-2" width="40" height="40" alt="Avatar">
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Message Input -->
            <div class="card-footer">
                <form action="<?= route_to('admin.messages.send', $conversation->id) ?>" method="POST" enctype="multipart/form-data" id="messageForm">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <label class="btn btn-outline-secondary mb-0" for="attachmentInput">
                                <i class="fas fa-paperclip"></i>
                                <input type="file" id="attachmentInput" name="attachment" class="d-none" accept="image/*,.pdf,.doc,.docx">
                            </label>
                        </div>
                        <input type="text" name="message" class="form-control" placeholder="Digite sua mensagem..." autocomplete="off" id="messageInput">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </div>
                    <div id="attachmentPreview" class="mt-2" style="display: none;">
                        <span class="badge badge-secondary">
                            <span id="attachmentName"></span>
                            <button type="button" class="btn btn-link btn-sm text-white p-0 ml-2" onclick="clearAttachment()">
                                <i class="fas fa-times"></i>
                            </button>
                        </span>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('css') ?>
<style>
    .chat-container {
        background: #f8f9fc;
    }
    
    .message-bubble {
        max-width: 70%;
        padding: 10px 15px;
        border-radius: 15px;
        word-wrap: break-word;
    }
    
    .message-mine {
        background: #4e73df;
        color: white;
        border-bottom-right-radius: 5px;
    }
    
    .message-mine .message-time {
        color: rgba(255,255,255,0.7) !important;
    }
    
    .message-other {
        background: white;
        border: 1px solid #e3e6f0;
        border-bottom-left-radius: 5px;
    }
    
    .message-sender {
        color: #4e73df;
    }
    
    .message-time {
        font-size: 0.7rem;
        margin-top: 5px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
    // Scroll to bottom on load
    var chatContainer = document.getElementById('chatContainer');
    chatContainer.scrollTop = chatContainer.scrollHeight;
    
    // Handle attachment preview
    document.getElementById('attachmentInput').addEventListener('change', function() {
        var file = this.files[0];
        if (file) {
            document.getElementById('attachmentName').textContent = file.name;
            document.getElementById('attachmentPreview').style.display = 'block';
        }
    });
    
    function clearAttachment() {
        document.getElementById('attachmentInput').value = '';
        document.getElementById('attachmentPreview').style.display = 'none';
    }
    
    function toggleMute() {
        $.post('<?= route_to('admin.messages.mute', $conversation->id) ?>', function(response) {
            if (response.success) {
                alert(response.message);
            }
        });
    }
    
    // Mark as read
    $.post('<?= route_to('admin.messages.read', $conversation->id) ?>');
    
    // Focus on input
    document.getElementById('messageInput').focus();
</script>
<?= $this->endSection() ?>
