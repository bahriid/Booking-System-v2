import { usePage } from '@inertiajs/react';
import { type ReactNode } from 'react';
import AdminLayout from '@/layouts/admin-layout';
import PartnerLayout from '@/layouts/partner-layout';
import DriverLayout from '@/layouts/driver-layout';
import { type SharedProps, type Breadcrumb } from '@/types';

interface RoleLayoutProps {
    children: ReactNode;
    title?: string;
    pageTitle?: string;
    breadcrumbs?: Breadcrumb[];
    toolbarActions?: ReactNode;
}

export default function RoleLayout({ children, ...props }: RoleLayoutProps) {
    const { auth } = usePage<SharedProps>().props;
    const role = auth?.user.role;

    switch (role) {
        case 'admin':
            return <AdminLayout {...props}>{children}</AdminLayout>;
        case 'partner':
            return <PartnerLayout {...props}>{children}</PartnerLayout>;
        case 'driver':
            return <DriverLayout {...props}>{children}</DriverLayout>;
        default:
            return <AdminLayout {...props}>{children}</AdminLayout>;
    }
}
