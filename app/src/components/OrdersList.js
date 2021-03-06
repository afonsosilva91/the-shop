import React from 'react';
import { Route } from 'react-router-dom'

import axios from 'axios'

import Grid from '@material-ui/core/Grid';
import Paper from '@material-ui/core/Paper';

import Table from '@material-ui/core/Table';
import TableBody from '@material-ui/core/TableBody';
import TableCell from '@material-ui/core/TableCell';
import TableHead from '@material-ui/core/TableHead';
import TableRow from '@material-ui/core/TableRow';


class OrdersList extends React.Component {

    constructor () {
        super()

        this.state = {
          orders: []
        }
    }

    componentWillMount() {
        axios.get('http://localhost:8081/orders')
            .then( response => this.setState({orders: response.data.data}) )
    }

    render() {
        return (
            <div className="order-list">
                <Grid container direction="row" justify="center" alignItems="center" xs={12}>
                    <Paper>
                        <Table>
                            <TableHead>
                                <TableRow>
                                    <TableCell>#ID</TableCell>
                                    <TableCell>Customer</TableCell>
                                    <TableCell>Order</TableCell>
                                    <TableCell>Discount</TableCell>
                                    <TableCell>Total</TableCell>
                                    <TableCell>Date</TableCell>
                                </TableRow>
                            </TableHead>
                            <TableBody>
                                {this.state.orders.map(order => {
                                    return (
                                        <Route render={({ history }) => (
                                            <TableRow key={order.id} className="order-row" onClick={(order) => { history.push({ pathname: '/details', state: { order: order }}) }}>
                                                <TableCell>{order.id}</TableCell>
                                                <TableCell>({order.customer.id}) {order.customer.name}</TableCell>
                                                <TableCell>{order.total_order}</TableCell>
                                                <TableCell>{order.total_discount}</TableCell>
                                                <TableCell>{order.total}</TableCell>
                                                <TableCell>{order.date}</TableCell>
                                            </TableRow>
                                        )} />
                                    );
                                })}
                            </TableBody>
                        </Table>
                    </Paper>
                </Grid>
            </div>
        );
    }
}

export default OrdersList;