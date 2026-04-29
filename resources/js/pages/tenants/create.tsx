import { Head, useForm, Link } from '@inertiajs/react';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { tenants } from '@/routes';
import { useEffect } from 'react';
import { toast } from 'sonner';

interface Bed {
    id: number;
    name: string;
    monthly_rate: number;
    room?: { name: string; floor: { name: string } };
}

export default function CreateTenant({ initialBed, availableBeds }: { initialBed: Bed | null, availableBeds: Bed[] }) {
    const { data, setData, post, processing, errors } = useForm({
        first_name: '',
        last_name: '',
        gender: '',
        phone: '',
        bed_id: initialBed ? initialBed.id.toString() : '',
        rent_amount: initialBed ? initialBed.monthly_rate.toString() : '',
    });

    // Update rent amount automatically if bed changes
    useEffect(() => {
        if (data.bed_id) {
            const selectedBed = availableBeds.find(b => b.id.toString() === data.bed_id) || initialBed;
            if (selectedBed && selectedBed.id.toString() === data.bed_id) {
                setData('rent_amount', selectedBed.monthly_rate.toString());
            }
        }
    }, [data.bed_id, availableBeds, initialBed]);

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post('/tenants', {
            onSuccess: () => toast.success('Tenant created and checked in successfully!'),
        });
    };

    return (
        <>
            <Head title="Create Tenant" />
            <div className="flex flex-1 flex-col gap-4 p-4 lg:p-8 max-w-3xl mx-auto w-full">
                <div>
                    <h1 className="text-2xl font-semibold tracking-tight">Create & Assign Tenant</h1>
                    <p className="text-sm text-muted-foreground">Register a new tenant and assign them to a bed immediately.</p>
                </div>

                <Card>
                    <form onSubmit={submit}>
                        <CardHeader>
                            <CardTitle>Personal Information</CardTitle>
                            <CardDescription>Basic details required to register the tenant.</CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-6">
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div className="space-y-2">
                                    <Label htmlFor="first_name">First Name</Label>
                                    <Input 
                                        id="first_name" 
                                        value={data.first_name} 
                                        onChange={e => setData('first_name', e.target.value)} 
                                        required 
                                    />
                                    {errors.first_name && <p className="text-sm text-destructive">{errors.first_name}</p>}
                                </div>
                                <div className="space-y-2">
                                    <Label htmlFor="last_name">Last Name</Label>
                                    <Input 
                                        id="last_name" 
                                        value={data.last_name} 
                                        onChange={e => setData('last_name', e.target.value)} 
                                        required 
                                    />
                                    {errors.last_name && <p className="text-sm text-destructive">{errors.last_name}</p>}
                                </div>
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div className="space-y-2">
                                    <Label htmlFor="gender">Gender</Label>
                                    <Select value={data.gender} onValueChange={val => setData('gender', val)} required>
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select gender" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="male">Male</SelectItem>
                                            <SelectItem value="female">Female</SelectItem>
                                            <SelectItem value="other">Other</SelectItem>
                                        </SelectContent>
                                    </Select>
                                    {errors.gender && <p className="text-sm text-destructive">{errors.gender}</p>}
                                </div>
                                <div className="space-y-2">
                                    <Label htmlFor="phone">Phone Number</Label>
                                    <Input 
                                        id="phone" 
                                        type="tel"
                                        value={data.phone} 
                                        onChange={e => setData('phone', e.target.value)} 
                                        required 
                                    />
                                    {errors.phone && <p className="text-sm text-destructive">{errors.phone}</p>}
                                </div>
                            </div>

                            <div className="border-t pt-6 mt-6">
                                <h3 className="text-lg font-medium mb-4">Assignment Details</h3>
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div className="space-y-2">
                                        <Label htmlFor="bed_id">Assign Bed</Label>
                                        <Select value={data.bed_id} onValueChange={val => setData('bed_id', val)} required>
                                            <SelectTrigger>
                                                <SelectValue placeholder="Select a bed" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                {initialBed && availableBeds.every(b => b.id !== initialBed.id) && (
                                                    <SelectItem value={initialBed.id.toString()}>
                                                        Bed {initialBed.name} ({initialBed.room?.name})
                                                    </SelectItem>
                                                )}
                                                {availableBeds.map(bed => (
                                                    <SelectItem key={bed.id} value={bed.id.toString()}>
                                                        Bed {bed.name} ({bed.room?.name})
                                                    </SelectItem>
                                                ))}
                                            </SelectContent>
                                        </Select>
                                        {errors.bed_id && <p className="text-sm text-destructive">{errors.bed_id}</p>}
                                    </div>
                                    <div className="space-y-2">
                                        <Label htmlFor="rent_amount">Agreed Monthly Rent (रु)</Label>
                                        <Input 
                                            id="rent_amount" 
                                            type="number"
                                            value={data.rent_amount} 
                                            onChange={e => setData('rent_amount', e.target.value)} 
                                            required 
                                        />
                                        {errors.rent_amount && <p className="text-sm text-destructive">{errors.rent_amount}</p>}
                                    </div>
                                </div>
                            </div>

                            <div className="flex items-center justify-end gap-4 border-t pt-6 mt-6">
                                <Button variant="outline" type="button" asChild>
                                    <Link href={tenants()}>Cancel</Link>
                                </Button>
                                <Button type="submit" disabled={processing}>
                                    Create & Check In
                                </Button>
                            </div>
                        </CardContent>
                    </form>
                </Card>
            </div>
        </>
    );
}

CreateTenant.layout = {
    breadcrumbs: [
        {
            title: 'Tenants',
            href: tenants(),
        },
        {
            title: 'Create',
            href: '/tenants/create',
        },
    ],
};
