import { Head } from '@inertiajs/react';

import AppearanceTabs from '@/components/appearance-tabs';
import HeadingSmall from '@/components/heading-small';
import { type BreadcrumbItem } from '@/types';

import AppLayout from '@/layouts/app-layout';
import SettingsLayout from '@/layouts/settings/layout';
import { appearance } from '@/routes';

import { useState } from 'react';
import axios from 'axios';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Appearance settings',
        href: appearance().url,
    },
];

export default function Appearance() {
    const [open, setOpen] = useState(false);
    const [messages, setMessages] = useState<{ role: 'user' | 'bot'; text: string }[]>([]);
    const [input, setInput] = useState('');

    const sendMessage = async () => {
        if (!input.trim()) return;
        const newMsg = { role: 'user', text: input };
        setMessages([...messages, newMsg]);
        setInput('');
        try {
            const res = await axios.post('/api/chatbot', { message: input });
            setMessages((prev) => [...prev, { role: 'bot', text: res.data.reply }]);
        } catch {
            setMessages((prev) => [...prev, { role: 'bot', text: '❌ Lỗi: Không thể kết nối chatbot.' }]);
        }
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Appearance settings" />

            <SettingsLayout>
                <div className="space-y-6">
                    <HeadingSmall title="Appearance settings" description="Update your account's appearance settings" />
                    <AppearanceTabs />
                </div>
            </SettingsLayout>

            {/* 🧠 Chatbot widget */}
            <div className="fixed bottom-5 right-5 z-50 text-black">
                {!open ? (
                    <button
                        onClick={() => setOpen(true)}
                        className="bg-blue-600 text-black rounded-full w-12 h-12 shadow-lg flex items-center justify-center hover:bg-blue-700"
                    >
                        💬
                    </button>
                ) : (
                    <div className="w-80 h-96 bg-white shadow-xl rounded-lg flex flex-col border border-gray-200">
                        <div className="bg-blue-600 text-black p-3 flex justify-between items-center rounded-t-lg">
                            <span>Trợ lý bán hàng</span>
                            <button onClick={() => setOpen(false)}>✕</button>
                        </div>

                        <div className="flex-1 overflow-y-auto p-3 space-y-2 text-sm">
                            {messages.map((m, i) => (
                                <div
                                    key={i}
                                    className={`p-2 rounded-lg max-w-[80%] ${
                                        m.role === 'user'
                                            ? 'bg-blue-100 text-right self-end ml-auto'
                                            : 'bg-gray-100 text-left'
                                    } text-black`}
                                >
                                    {m.text}
                                </div>
                            ))}
                        </div>

                        <div className="border-t p-2 flex gap-2">
                            <input
                                value={input}
                                onChange={(e) => setInput(e.target.value)}
                                onKeyDown={(e) => e.key === 'Enter' && sendMessage()}
                                placeholder="Nhập câu hỏi..."
                                className="flex-1 border rounded px-2 py-1 text-sm focus:outline-none text-black placeholder-black"
                            />
                            <button
                                onClick={sendMessage}
                                className="bg-blue-600 text-black px-3 rounded hover:bg-blue-700"
                            >
                                Gửi
                            </button>
                        </div>
                    </div>
                )}
            </div>

        </AppLayout>
    );
}
